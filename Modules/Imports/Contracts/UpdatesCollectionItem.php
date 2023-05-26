<?php

namespace Modules\Imports\Contracts;

use Modules\Imports\Models\CollectionItem;

interface UpdatesCollectionItem
{
    public function update(CollectionItem $collectionItem, array $input): void;
}
