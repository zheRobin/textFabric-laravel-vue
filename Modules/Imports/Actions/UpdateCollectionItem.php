<?php

namespace Modules\Imports\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Imports\Contracts\UpdatesCollectionItem;
use Modules\Imports\Models\CollectionItem;

class UpdateCollectionItem implements UpdatesCollectionItem
{
    public function update(User $user, CollectionItem $collectionItem, array $input): void
    {
        Gate::forUser($user)->authorize('update', $collectionItem);

        Validator::make($input, [
            'items' => ['required', 'array']
        ])->validateWithBag('updateCollectionItem');

        $collectionItem->update([
            'data' => $input['items']
        ]);
    }
}
