<?php

namespace Modules\Jetstream\Actions;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Events\AddingTeam;
use Laravel\Jetstream\Jetstream;
use Modules\Subscriptions\Enums\SubscriptionPlanEnum;
use Modules\Subscriptions\Models\Plan;

class CreateTeam implements CreatesTeams
{
    /**
     * Validate and create a new team for the given user.
     *
     * @param  array<string, string>  $input
     */
    public function create(User $user, array $input): Team
    {
        Gate::forUser($user)->authorize('create', Jetstream::newTeamModel());

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createTeam');

        AddingTeam::dispatch($user);

        return DB::transaction(function () use ($user, $input) {
            $team = tap($user->ownedTeams()->create([
                'name' => $input['name'],
                'personal_team' => false,
            ]), function (Team $team) {
                $this->createPlanSubscription($team);
            });
            $user->switchTeam($team);

            return $team;
        });
    }

    /**
     * @param Team $team
     * @return void
     */
    protected function createPlanSubscription(Team $team): void
    {
        $plan = Plan::query()
            ->where('slug', SubscriptionPlanEnum::PRO->slug())
            ->firstOrFail();

        $team->newPlanSubscription($plan);
    }
}
