<?php

namespace Modules\Collections\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Collections\Models\Collection;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;

class CollectionPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): bool|null
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Collection $collection): bool
    {
        return $user->currentTeam->ownsCollection($collection);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if (!$user->currentTeam->planSubscription
            ->canUseFeature(SubscriptionFeatureEnum::COLLECTIONS_LIMIT)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Collection $collection): bool
    {
        return $user->currentTeam->ownsCollection($collection);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Collection $collection): bool
    {
        return $user->currentTeam->is($collection->team);
    }
}
