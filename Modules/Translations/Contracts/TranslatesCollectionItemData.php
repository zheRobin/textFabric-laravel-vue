<?php

namespace Modules\Translations\Contracts;

use Modules\Imports\Models\CollectionItem;

interface TranslatesCollectionItemData
{
    public function translate(CollectionItem $collectionItem, array $input): array;
}
