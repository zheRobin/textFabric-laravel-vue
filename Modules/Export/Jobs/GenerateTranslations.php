<?php

namespace Modules\Export\Jobs;

use DeepL\Translator;
use Illuminate\Bus\Batchable;
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
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $languages;
    protected $text;
    protected $value;
    protected $export;
    protected $textkey;

    public function __construct($languages, $value, $key, $export, $textkey)
    {
        $this->languages = $languages;
        $this->value = $value;
        $this->key = $key;
        $this->export = $export;
        $this->textkey = $textkey;
    }

    public function handle(Preset $preset): void
    {
        $translator = app(Translator::class);
        $result = [];
        foreach ($this->languages as $index => $lang) {
            $translate = $translator->translateText($this->value, null, $lang);
            $result[$this->textkey][$lang] = $translate->text;
        }

        $this->export->data = [...$this->export->data, $result];
        $this->export->save();
    }
}
