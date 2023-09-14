<?php

namespace Modules\Translations\Actions;

use DeepL\DeepLException;
use DeepL\Translator;
use Illuminate\Support\Facades\Validator;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use Modules\Translations\Contracts\TranslatesText;

class TranslateText implements TranslatesText
{
    /**
     * @throws DeepLException
     */
    public function translate(array $input): string
    {
        Validator::make($input, [
            'content' => ['required', 'string'],
            'languageCode' => ['required', 'exists:languages,code']
        ])->validateWithBag('translateText');

        $translator = app(Translator::class);

        // ------------------------------------------------
        // count subscription plan ------------------------
        request()->user()->currentTeam->planSubscription
            ->recordFeatureUsage(SubscriptionFeatureEnum::DEEPL_REQUESTS);
        request()->user()->currentTeam->planSubscription
            ->recordFeatureUsage(SubscriptionFeatureEnum::API_REQUESTS);
        // ------------------------------------------------

        return $translator->translateText($input['content'], null, $input['languageCode']);
    }
}
