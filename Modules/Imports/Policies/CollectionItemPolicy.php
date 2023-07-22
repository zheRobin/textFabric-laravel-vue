<?php

namespace Modules\Imports\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Imports\Models\CollectionItem;

class CollectionItemPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): bool|null
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->ownsTeam($user->currentTeam) &&
            $user->hasTeamRole($user->currentTeam, 'viewer')) {
            return false;
        }

        return null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CollectionItem $collectionItem): bool
    {
        return $user->currentTeam->ownsCollection($collectionItem->collection);
    }
}
