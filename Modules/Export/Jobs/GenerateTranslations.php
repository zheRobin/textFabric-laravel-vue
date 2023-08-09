<?php

namespace Modules\Export\Jobs;

use DeepL\Translator;
use Modules\Export\Events\GetNotificationWhenQueueEndEvents;
use Modules\Export\Models\CompilationExport;
use Modules\Presets\Models\Preset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Export\Models\QueueProgress;

class GenerateTranslations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $value;
    protected $languages;
    protected $id;

    public function __construct($value, $languages, $id)
    {
        $this->value = $value;
        $this->languages = $languages;
        $this->id = $id;
    }

    public function handle(Preset $preset): void
    {
        $queueProgress = new QueueProgress;
        $queueProgress->job_id = $this->job->getJobId(); // Отримайте id чергового завдання
        $queueProgress->status = 'pending';
        $queueProgress->save();

        $result = [];
        $translator = app(Translator::class);
        $count = 1;
        foreach ($this->value as $key => $item) {
            $result[$key][key($item)] = $item[key($item)];
            foreach ($item as $text) {
                foreach ($this->languages as $index => $lang) {
                    $translate = $translator->translateText($text, null, $lang);
                    $result[$key][$lang] = array_map(fn($el) => $el->text, $translate);
                }

            }

            if ($this->queueShouldStop($this->job->getJobId())) {
                return; // Завдання не буде продовжувати виконання
            }

            $queueProgress->status = 'in_progress';
            $queueProgress->progress = 100 * ($count++/count($this->value));
            $queueProgress->save();
        }

        $exports = new CompilationExport();

        $data = $exports->find($this->id);
        $data->data = $result;

        $data->save();

        event(new GetNotificationWhenQueueEndEvents('The translation was successful!'));
    }

    public function queueShouldStop($id): bool
    {
        return QueueProgress::where('job_id', $id)->first()->status === 'stop';
    }
}
