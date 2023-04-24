<?php

namespace Modules\Subscriptions\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Subscriptions\Contracts\UpdatesPlanSubscription;
use Modules\Subscriptions\Models\PlanSubscription;

class UpdatePlanSubscription implements UpdatesPlanSubscription
{
    /**
     * @param User $user
     * @param PlanSubscription $planSubscription
     * @param array $input
     * @return void
     */
    public function update(User $user, PlanSubscription $planSubscription, array $input): void
    {
        Gate::forUser($user)->authorize('update', $planSubscription);

        Validator::make($input, [
            'plan_id' => ['required', 'exists:plans,id'],
            'ends_at' => ['required', 'date'],
            'on_trial' => ['required', 'boolean']
        ], [], ['plan_id' => 'plan',])->validateWithBag('updatePlanSubscription');

        $planSubscription->fill([
            'plan_id' => $input['plan_id'],
            'ends_at' => $input['ends_at'],
            'on_trial' => $input['on_trial'],
        ])->save();
    }
}
