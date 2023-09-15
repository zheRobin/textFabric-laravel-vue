<?php

namespace Modules\Jetstream\Auth;

use App\Models\User;
use Modules\Subscriptions\Enums\SubscriptionPlanEnum;

class ApiFeaturesUserAbilities
{
    public function useApiFeatures(User $user): bool
    {
        $team = $user->currentTeam;
        $subscriptionPlan = $team?->planSubscription?->plan;

        if ($subscriptionPlan &&
            in_array($subscriptionPlan->slug, [SubscriptionPlanEnum::ENTERPRISE->slug(), SubscriptionPlanEnum::UNLIMITED->slug()]) &&
            $user->hasTeamRole($team, 'admin')) {
            return true;
        }

        return false;
    }
}
