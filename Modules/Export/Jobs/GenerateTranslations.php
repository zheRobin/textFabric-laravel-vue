<?php

namespace Modules\Export\Jobs;

use DeepL\Translator;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Str;
use Modules\Export\Models\ExportCollectionItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateTranslations implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected array $languages,
        protected ExportCollectionItem $exportCollectionItem,
    ) {
    }

    public function handle(): void
    {
        $translator = app(Translator::class);

        $translations = [];
        foreach ($this->languages as $languageCode) {
            $completions = $this->exportCollectionItem->completions;

            foreach ($completions as $completion) {
                if (isset($completion['header']) &&
                    isset($completion['value'])) {
                    $translations[] = [
                        'header' => $this->translatedHeader($completion['header'], $languageCode),
                        'value' => empty($completion['value'])
                            ? $completion['value']
                            : $translator->translateText($completion['value'], null, $languageCode)->text
                    ];
                }
            }
        }

        $this->exportCollectionItem->update([
            'translations' => $translations
        ]);
    }

    protected function translatedHeader(string $completionHeader, string $languageCode): string
    {
        return implode('_', [$completionHeader, Str::slug($languageCode)]);
    }

    protected function completionHasTranslation(string $languageCode, string $completionHeader): bool
    {
        if (is_null($this->exportCollectionItem->translations)) {
            return false;
        }

        $translatedHeader = $this->translatedHeader($completionHeader, $languageCode);

        $foundedHeaders = array_column($this->exportCollectionItem->translations, 'header');

        return in_array($translatedHeader, $foundedHeaders);
    }
}
