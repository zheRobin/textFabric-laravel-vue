<?php

namespace Modules\Translations\Actions;

use DeepL\DeepLException;
use DeepL\Translator;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use Modules\Translations\Contracts\TranslatesData;
use Modules\Translations\Models\Language;

class TranslateData implements TranslatesData
{
    /**
     * @throws DeepLException
     */
    public function translate(array $data, Language $language): array
    {
        if (empty($data)) {
            return $data;
        }

        $translator = app(Translator::class);

        $attributes = array_map(fn ($value) => strval($value), $data);

        // ------------------------------------------------
        // count subscription plan ------------------------
        request()->user()->currentTeam->planSubscription
            ->recordFeatureUsage(SubscriptionFeatureEnum::DEEPL_REQUESTS);
        request()->user()->currentTeam->planSubscription
            ->recordFeatureUsage(SubscriptionFeatureEnum::API_REQUESTS);
        // ------------------------------------------------

        return array_combine(
            array_keys($attributes),
            array_column(
                $translator->translateText(array_values($attributes), null, $language->code),
                'text'
            )
        );
    }
}
