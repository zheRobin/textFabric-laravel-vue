<?php

namespace Modules\Imports\Policies;

use App\Models\User;
use Modules\Imports\Models\CollectionItem;

class CollectionItemPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CollectionItem $collectionItem): bool
    {
        return $user->currentTeam->ownsCollection($collectionItem->collection);
    }
}
