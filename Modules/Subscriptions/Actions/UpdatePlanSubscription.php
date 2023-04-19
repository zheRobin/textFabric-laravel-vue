<?php

namespace Modules\Subscriptions\Actions;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Subscriptions\Contracts\UpdatesPlanSubscription;
use Modules\Subscriptions\Models\PlanSubscription;
use Modules\Subscriptions\Services\Period;

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
            'ends_at' => ['nullable', 'date'],
            'on_trial' => ['required', 'boolean']
        ], [], [
            'plan_id' => 'plan',
            'ends_at' => 'invoice ends at',
        ])->validateWithBag('updatePlanSubscription');

        $planSubscription->fill([
            'plan_id' => $input['plan_id'],
            'ends_at' => $input['ends_at'],
        ]);

        // add trial period
        if ($input['on_trial'] &&
            !$planSubscription->active_trial) {
            $trial = new Period($planSubscription->plan->trial_period, Carbon::now());

            $planSubscription->fill(['trial_ends_at' => $trial->getEndDate()]);
        }

        // remove trial period
        if (!$input['on_trial'] &&
            $planSubscription->active_trial) {
            $planSubscription->fill(['trial_ends_at' => null]);
        }

        $planSubscription->save();
    }
}
