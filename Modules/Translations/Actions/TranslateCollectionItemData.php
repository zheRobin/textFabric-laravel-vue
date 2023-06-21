<?php

namespace Modules\Translations\Actions;

use DeepL\Translator;
use Modules\Imports\Models\CollectionItem;
use Modules\Translations\Contracts\TranslatesCollectionItemData;

class TranslateCollectionItemData implements TranslatesCollectionItemData
{
    public function translate(CollectionItem $collectionItem): array
    {
        $translator = app(Translator::class);

        $cells = [];
        $cellsToTranslate = array_filter($collectionItem->data, fn($el) => isset($el['value']) && $el['value']);
        $cellsToTranslate = array_values($cellsToTranslate);

        $translatedCells = $translator->translateText(array_column($cellsToTranslate, 'value'), null, 'UK');

        array_walk($translatedCells, function ($el, $key) use (&$cellsToTranslate) {
            $cellsToTranslate[$key]['value'] = $el->text;
        });

        $untranslatedCells = array_filter($collectionItem->data, fn($el) => empty($el['value']));

        return array_merge(
            $cellsToTranslate,
            $untranslatedCells
        );
    }
}
