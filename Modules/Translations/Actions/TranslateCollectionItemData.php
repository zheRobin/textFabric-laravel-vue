<?php

namespace Modules\Translations\Actions;

use DeepL\Translator;
use Illuminate\Support\Facades\Validator;
use Modules\Imports\Models\CollectionItem;
use Modules\Translations\Contracts\TranslatesCollectionItemData;
use Modules\Translations\Models\Language;

class TranslateCollectionItemData implements TranslatesCollectionItemData
{
    public function translate(CollectionItem $collectionItem, array $input): array
    {
        // TODO: authorization

        Validator::make($input, [
            'languageCode' => ['required', 'exists:languages,code']
        ])->validateWithBag('translateItemData');

        $translator = app(Translator::class);

        $allCellsValues = array_map(
            fn ($el) => array_key_exists('value', $el) ? $el['value'] : null,
            $collectionItem->data
        );

        $cellsValuesToTranslate = array_filter($allCellsValues);

        $translatedCellsValues = $translator->translateText($cellsValuesToTranslate, null, $input['languageCode']);
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
