<?php

namespace Modules\Fortify\Actions;

use App\Models\Team;
use App\Models\User;
use App\Rules\GoogleRecaptcha;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Modules\Subscriptions\Enums\SubscriptionPlanEnum;
use Modules\Subscriptions\Models\Plan;

class CreateNewUser implements CreatesNewUsers
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
            'position' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'employees' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'google_recaptcha' => ['required', new GoogleRecaptcha]
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'position' => $input['position'],
                'company' => $input['company'],
                'employees' => $input['employees'],
                'email' => $input['email'],
                'phone_number' => $input['phone_number'],
                'password' => Hash::make($input['password']),
            ]), function (User $user) {
                $this->createTeam($user);
            });
        });
    }

    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): void
    {
        tap(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->company, 2)[0]."'s Team",
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
            ->where('slug', SubscriptionPlanEnum::BASE->slug())
            ->firstOrFail();

        $team->newPlanSubscription($plan);
    }
}
