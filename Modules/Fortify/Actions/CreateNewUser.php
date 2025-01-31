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
use Modules\Collections\Actions\CreateDemoCollection;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use Modules\Subscriptions\Enums\SubscriptionPlanEnum;
use Modules\Subscriptions\Models\Plan;
use Illuminate\Support\Facades\Mail;
use Modules\Fortify\Mail\RegistrationEmail;
use App\Jobs\SendNewTeamAccountEmail;
use App\Jobs\SendNewTeamAccountToWoodpecker;

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

        $data = [
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'position' => $input['position'],
            'email' => $input['email'],
            'company' => $input['company'],
            'employees' => $input['employees'],
            'phone_number' => $input['phone_number']
        ];

        $content = "
        First Name: {$data['first_name']}
        Last Name: {$data['last_name']}
        Position: {$data['position']}
        Email: {$data['email']}
        Company: {$data['company']}
        Employees: {$data['employees']}
        Phone number: {$data['phone_number']}
    ";

        $recipientEmail = explode(';', config('app')['mail_to']);
        $subject = 'New Team Account Created';

        foreach ($recipientEmail as $mail) {
            SendNewTeamAccountEmail::dispatch($mail, $subject, $content);
        }

        SendNewTeamAccountToWoodpecker::dispatch($data['email'], $data['first_name'], $data['last_name']);

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
            ]), function (User $user) use ($input) {
                $this->createTeam($user, $input['locale']);
            });
        });
    }

    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user, string $locale): void
    {
        tap(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->first_name, 2)[0]."'s Team",
            'personal_team' => true,
        ]), function (Team $team) use ($locale, $user) {
            $this->createPlanSubscription($team, $user);
            $demoCollectionCreator = app(CreateDemoCollection::class);
            $demoCollectionCreator->create($user, $team, $locale);
        });
    }

    /**
     * @param Team $team
     * @return void
     */
    protected function createPlanSubscription(Team $team, User $user): void
    {
        $plan = Plan::query()
            ->where('slug', SubscriptionPlanEnum::TRIAL->slug())
            ->firstOrFail();

        $team->newPlanSubscription($plan);

        $user->currentTeam->planSubscription
            ->recordFeatureUsage(SubscriptionFeatureEnum::COLLECTIONS_LIMIT);
    }
}
