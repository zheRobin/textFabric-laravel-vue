<?php

namespace Modules\Imports\Actions;

use Illuminate\Support\Facades\Validator;
use Modules\Imports\Contracts\UpdatesCollectionItem;
use Modules\Imports\Models\CollectionItem;

class UpdateCollectionItem implements UpdatesCollectionItem
{
    public function update(CollectionItem $collectionItem, array $input): void
    {
        // authorize

        Validator::make($input, [
            'items' => ['required', 'array']
        ])->validateWithBag('updateCollectionItem');

        $collectionItem->update([
            'data' => $input['items']
        ]);
    }
}
