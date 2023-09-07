<?php

namespace Modules\Subscriptions\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Subscriptions\Models\Plan;
use Modules\Subscriptions\Models\PlanSubscription;
use Modules\Subscriptions\Services\Period;

trait HasPlanSubscription
{
    /**
     * @return MorphOne
     */
    public function planSubscription(): MorphOne
    {
        return $this->morphOne(PlanSubscription::class, 'subscriber', 'subscriber_type', 'subscriber_id');
    }

    /**
     * @param Plan $plan
     * @param Carbon|null $startDate
     * @return PlanSubscription
     */
    public function newPlanSubscription(Plan $plan, Carbon $startDate = null): PlanSubscription
    {
        $period = new Period($plan->trial_period ?? $plan->invoice_period, $startDate ?? Carbon::now());

        return $this->planSubscription()->create([
            'plan_id' => $plan->getKey(),
            'ends_at' => $period->getEndDate(),
            'on_trial' => boolval($plan->trial_period),
        ]);
    }

    /**
     * @return void
     */
    protected static function bootHasPlanSubscription()
    {
        static::deleted(function ($subscriber) {
            if ($subscriber->planSubscription) {
                $subscriber->planSubscription->delete();
            }
        });
    }
}
