<?php

namespace Modules\Jetstream\Auth;

use App\Models\User;
use Modules\Subscriptions\Enums\SubscriptionPlanEnum;

class ApiFeaturesUserAbilities
{
    public function useApiFeatures(User $user): bool
    {
        $team = $user->currentTeam;

        if ($team &&
            $team->planSubscription &&
            $team->planSubscription->plan->slug === SubscriptionPlanEnum::ENTERPRISE->slug() &&
            $user->hasTeamRole($team, 'admin')) {
            return true;
        }

        return false;
    }
}
