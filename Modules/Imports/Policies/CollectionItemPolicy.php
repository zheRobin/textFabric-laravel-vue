<?php

namespace Modules\Imports\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Imports\Models\CollectionItem;

class CollectionItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CollectionItem $collectionItem): bool
    {
        return $user->currentTeam->ownsCollection($collectionItem->collection);
    }
}
