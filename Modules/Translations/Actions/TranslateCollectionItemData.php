<?php

namespace Modules\Translations\Actions;

use DeepL\DeepLException;
use DeepL\Translator;
use Illuminate\Support\Facades\Validator;
use Modules\Imports\Models\CollectionItem;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use Modules\Translations\Contracts\TranslatesCollectionItemData;

class TranslateCollectionItemData implements TranslatesCollectionItemData
{
    /**
     * @throws DeepLException
     */
    public function translate(CollectionItem $collectionItem, array $input): array
    {
        // TODO: authorization

        Validator::make($input, [
            'languageCode' => ['required', 'exists:languages,code']
        ])->validateWithBag('translateItemData');

        $translator = app(Translator::class);

        $allCellsValues = array_map(
            fn ($el) => array_key_exists('value', $el) ? strval($el['value']) : null,
            $collectionItem->data
        );

        $cellsValuesToTranslate = array_filter($allCellsValues);

        $translatedCellsValues = $translator->translateText($cellsValuesToTranslate, null, $input['languageCode']);

        // ------------------------------------------------
        // count subscription plan ------------------------
        request()->user()->currentTeam->planSubscription
            ->recordFeatureUsage(SubscriptionFeatureEnum::DEEPL_REQUESTS);
        request()->user()->currentTeam->planSubscription
            ->recordFeatureUsage(SubscriptionFeatureEnum::API_REQUESTS);
        // ------------------------------------------------

        $translatedCellsValues = array_combine(
            array_keys($cellsValuesToTranslate),
            array_map(fn ($el) => $el->text, $translatedCellsValues)
        );

        $allCellsValues = array_replace($allCellsValues, $translatedCellsValues);

        return array_map(function ($cell) use (&$allCellsValues) {
            $cell['value'] = array_shift($allCellsValues);

            return $cell;
        }, $collectionItem->data);
    }
}
