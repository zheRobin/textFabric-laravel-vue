<?php

namespace Modules\Subscriptions\Contracts;

use App\Models\User;
use Modules\Subscriptions\Models\PlanSubscription;

interface UpdatesPlanSubscription
{
    /**
     * @param User $user
     * @param PlanSubscription $planSubscription
     * @param array $input
     * @return void
     */
    public function update(User $user, PlanSubscription $planSubscription, array $input): void;
}
