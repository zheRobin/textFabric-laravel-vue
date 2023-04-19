<?php

namespace Modules\Subscriptions\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Subscriptions\Models\PlanSubscription;

class PlanSubscriptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PlanSubscription $planSubscription): bool
    {
        return $user->isSuperAdmin();
    }
}
