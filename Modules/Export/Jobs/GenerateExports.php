<?php

namespace Modules\Export\Jobs;

use App\Models\Compilations;
use Carbon\Carbon;
use Modules\Export\Models\CompilationExport;
use Modules\OpenAI\Contracts\BuildsParams;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use Modules\Translations\Models\Language;
use Modules\Presets\Models\Preset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\User;
use Modules\Export\Events\GetNotificationWhenQueueEndEvents;
use Modules\Export\Models\QueueProgress;

class GenerateExports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $compilationId;
    protected $items;
    protected $user;
    protected $team_id;
    protected $collection_id;

    public function __construct($compilationId, $items, $user_id, $team_id, $collection_id)
    {
        $this->compilationId = $compilationId;
        $this->items = $items;
        $this->user = User::where('id', $user_id)->get()->first();
        $this->team_id = $team_id;
        $this->collection_id = $collection_id;
    }

    public function handle(Preset $preset): void
    {
        $queueProgress = new QueueProgress;
        $queueProgress->job_id = $this->job->getJobId(); // Get id of the task
        $queueProgress->status = 'pending';
        $queueProgress->save();

        $compilationModel = Compilations::where('id', $this->compilationId)->first();

        $compilationName = $compilationModel->name;
        $presetIds = $compilationModel->preset_ids;

        $builder = app(BuildsParams::class);
        $result = [];
        $count = 1;
        foreach ($presetIds as $id) {
            $pres = $preset->where('id', $id)->first();

            $result[$compilationName . '_' . $pres->name] = [];
            foreach ($this->items as $index => $item) {
                $params = $builder->build($this->user, $pres, $item);
                $response = OpenAI::chat()->create($params);

                // ------------------------------------------------
                // count subscription plan ------------------------
                $this->user->currentTeam->planSubscription
                    ->recordFeatureUsage(SubscriptionFeatureEnum::OPENAI_REQUESTS);
                $this->user->currentTeam->planSubscription
                    ->recordFeatureUsage(SubscriptionFeatureEnum::API_REQUESTS);
                // ------------------------------------------------

                $content = $response->choices[0]->message->content;
                $result[$compilationName . '_' . $pres->name]['def'][$index] = $content;
            }

            if ($this->queueShouldStop($this->job->getJobId())) {
                return; // The task will not continue to run
            }

            $queueProgress->status = 'in_progress';
            $queueProgress->progress = 100 * ($count++ / count($presetIds));
            $queueProgress->save();
        }

        $now = Carbon::now();
        $exports = new CompilationExport();
        $fullDate = $now->format('Y-m-d H:i:s');

        $resultData[$fullDate] = [];
        foreach ($result as $name => $value) {
            $resultData[$fullDate][$name] = $value;
        }

        $currentDateTime = Carbon::now();

        $exports->compilation_id = $this->compilationId;
        $exports->team_id = $this->team_id;
        $exports->data = $resultData[$fullDate];
        $exports->name = $compilationName . '_' . $currentDateTime->format('Y-m-d H:i:s');
        $exports->collection_id = $this->collection_id;

        $exports->save();

        $queueProgress->status = 'done';
        $queueProgress->save();

        event(new GetNotificationWhenQueueEndEvents('Generation is complete!'));
    }

    public function queueShouldStop($id): bool
    {
        return QueueProgress::where('job_id', $id)->first()->status === 'stop';
    }
}
