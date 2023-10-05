<?php

namespace Modules\Export\Jobs;

use App\Models\Compilations;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Modules\Export\Models\Export;
use Modules\Imports\Models\CollectionItem;
use Modules\OpenAI\Contracts\BuildsParams;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

class ProcessExportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3; // The number of times the job may be attempted.
    public bool $failOnTimeout = false; // Indicate if the job should be marked as failed on timeout.
    public int $timeout = 120; // The number of seconds the job can run before timing out.
    public array $backoff = [16, 31, 61]; // The number of seconds to wait before retrying the job.
    public int $maxExceptions = 3; // The maximum number of unhandled exceptions to allow before failing.

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected User $user,
        protected Compilations $compilation,
        protected CollectionItem $collectionItem,
        protected Export $export,
    ) {
    }

    /**
     * @return array
     */
    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        $builder = app(BuildsParams::class);
        $planSubscription = $this->user->currentTeam->planSubscription;

        $completions = [];
        foreach ($this->compilation->getPresets() as $preset) {
            $params = $builder->build($this->user, $preset, $this->collectionItem);

            // ------------------------------------------------
            // check subscription plan limit ------------------
            if (!$planSubscription->canUseFeature(SubscriptionFeatureEnum::OPENAI_REQUESTS) ||
                !$planSubscription->canUseFeature(SubscriptionFeatureEnum::API_REQUESTS)) {
                $this->batch()->cancel();
                throw new \Exception(trans('Your team is out of remaining requests for this month. Please adjust your plan or wait until the next month.'), 403);
            }
            // ------------------------------------------------

            $response = OpenAI::chat()->create($params);

            // ------------------------------------------------
            // count subscription plan ------------------------
            $planSubscription->recordFeatureUsage(SubscriptionFeatureEnum::OPENAI_REQUESTS);
            $planSubscription->recordFeatureUsage(SubscriptionFeatureEnum::API_REQUESTS);
            // ------------------------------------------------

            $header = Str::slug($this->compilation->name) . '_' . Str::slug($preset->name);
            $completions[] = [
                'header' => $header,
                'value' => $this->formatResponse($response),
            ];
        }

        $this->export->items()->create([
            'data' => $this->collectionItem->getCells(),
            'completions' => $completions,
        ]);
    }

    /**
     * @param CreateResponse $response
     * @return mixed|null
     */
    protected function formatResponse(CreateResponse $response): mixed
    {
        $firstChoice = $response->choices[0]->toArray();

        return $firstChoice['message']['content'] ?? null;
    }
}
