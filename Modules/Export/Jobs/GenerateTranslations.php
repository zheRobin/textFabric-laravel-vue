<?php

namespace Modules\Export\Jobs;

use App\Models\User;
use DeepL\DeepLException;
use DeepL\Translator;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Str;
use Modules\Export\Models\ExportCollectionItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;

class GenerateTranslations implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2; // The number of times the job may be attempted.
    public bool $failOnTimeout = false; // Indicate if the job should be marked as failed on timeout.
    public int $timeout = 150; // The number of seconds the job can run before timing out.
    public array $backoff = [1, 5]; // The number of seconds to wait before retrying the job.
    public int $maxExceptions = 2; // The maximum number of unhandled exceptions to allow before failing.

    public function __construct(
        protected User $user,
        protected array $languages,
        protected ExportCollectionItem $exportCollectionItem,
    ) {
    }

    /**
     * @throws DeepLException
     */
    public function handle(): void
    {
        $translator = app(Translator::class);

        $translations = [];
        foreach ($this->languages as $languageCode) {
            $completions = $this->exportCollectionItem->completions;

            foreach ($completions as $completion) {
                if (isset($completion['header']) && isset($completion['value'])) {
                    $translations[] = [
                        'header' => $this->translatedHeader($completion['header'], $languageCode),
                        'value' => empty($completion['value'])
                            ? $completion['value']
                            : $translator->translateText($completion['value'], null, $languageCode)->text
                    ];

                    if (!empty($completion['value'])) {
                        // ------------------------------------------------
                        // count subscription plan ------------------------
                        $this->user->currentTeam->planSubscription
                            ->recordFeatureUsage(SubscriptionFeatureEnum::DEEPL_REQUESTS);
                        $this->user->currentTeam->planSubscription
                            ->recordFeatureUsage(SubscriptionFeatureEnum::API_REQUESTS);
                        // ------------------------------------------------
                    }
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
