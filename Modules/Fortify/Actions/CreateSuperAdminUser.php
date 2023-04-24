<?php

namespace Modules\Fortify\Actions;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Fortify\Contracts\CreatesSuperAdminUser;
use Modules\Subscriptions\Enums\SubscriptionPlanEnum;
use Modules\Subscriptions\Models\Plan;

class CreateSuperAdminUser implements CreatesSuperAdminUser
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::forceCreate([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'is_admin' => true,
            ]), function ($user) {
                $this->createTeam($user);
            });
        });
    }

    /**
     * Create a personal team for the admin.
     */
    protected function createTeam(User $user): void
    {
        tap(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->first_name, 2)[0]."'s Team",
            'personal_team' => true,
        ]), function (Team $team) {
            $this->createPlanSubscription($team);
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
