<?php

use App\Http\Controllers\LocalizationController;
use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Http\Controllers\CurrentTeamController;
use Modules\Jetstream\Controllers\ApiTokenController;
use Modules\Jetstream\Controllers\CurrentUserController;
use Laravel\Jetstream\Http\Controllers\Inertia\OtherBrowserSessionsController;
use Laravel\Jetstream\Http\Controllers\Inertia\PrivacyPolicyController;
use Laravel\Jetstream\Http\Controllers\Inertia\ProfilePhotoController;
use Laravel\Jetstream\Http\Controllers\Inertia\TeamMemberController;
use Laravel\Jetstream\Http\Controllers\Inertia\TermsOfServiceController;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController;
use Laravel\Jetstream\Http\Controllers\TeamInvitationController;
use Laravel\Jetstream\Jetstream;
use Modules\Jetstream\Controllers\AppSettingsController;
use Modules\Jetstream\Controllers\LogoController;
use Modules\Jetstream\Controllers\TeamController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Subscriptions\Enums\SubscriptionPlanEnum;
use Modules\Jetstream\Controllers\DashboardController;

Route::post('/change-language', [LocalizationController::class, 'changeLanguage']);

Route::group(['prefix' => LaravelLocalization::setLocale()],
    function (){
    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });

    Route::post('/dashboard/update', [DashboardController::class, 'update'])->name('dashboard.update');
    Route::group(['middleware' => config('jetstream.middleware', ['web'])], function () {
        if (Jetstream::hasTermsAndPrivacyPolicyFeature()) {
            Route::get('/terms-of-service', [TermsOfServiceController::class, 'show'])->name('terms.show');
            Route::get('/privacy-policy', [PrivacyPolicyController::class, 'show'])->name('policy.show');
        }

        $authMiddleware = config('jetstream.guard')
            ? 'auth:'.config('jetstream.guard')
            : 'auth';

        $authSessionMiddleware = config('jetstream.auth_session', false)
            ? config('jetstream.auth_session')
            : null;


        Route::group(
            ['middleware' => array_values(array_filter([$authMiddleware, $authSessionMiddleware]))]
            , function () {
            // User & Profile...
            Route::get('/user/profile', [UserProfileController::class, 'show'])
                ->name('profile.show');

            Route::delete('/user/other-browser-sessions', [OtherBrowserSessionsController::class, 'destroy'])
                ->name('other-browser-sessions.destroy');

            Route::delete('/user/profile-photo', [ProfilePhotoController::class, 'destroy'])
                ->name('current-user-photo.destroy');

            if (Jetstream::hasAccountDeletionFeatures()) {
                Route::delete('/user', [CurrentUserController::class, 'destroy'])
                    ->name('current-user.destroy');
            }

            Route::group(['middleware' => 'verified'], function () {
                // API...
                if (Jetstream::hasApiFeatures()) {
                    $apiFeaturesMiddleware = [
                        'subscribed',
                        'subscription:' . implode(',', [SubscriptionPlanEnum::ENTERPRISE->slug(), SubscriptionPlanEnum::UNLIMITED->slug()]),
                        'team.role:admin',
                    ];

                    Route::group(['middleware' => $apiFeaturesMiddleware], function () {
                        Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
                        Route::post('/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
                        Route::put('/api-tokens/{token}', [ApiTokenController::class, 'update'])->name('api-tokens.update');
                        Route::delete('/api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
                    });
                }

                // Teams...
                if (Jetstream::hasTeamFeatures()) {
                    Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
                    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
                    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
                    Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
                    Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
                    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
                    Route::put('/teams/{team}/toggle-disabled', [TeamController::class, 'toggleDisabled'])->name('teams.toggle-disabled');
                    Route::put('/current-team', [CurrentTeamController::class, 'update'])->name('current-team.update');
                    Route::post('/teams/{team}/members', [TeamMemberController::class, 'store'])->name('team-members.store');
                    Route::put('/teams/{team}/members/{user}', [TeamMemberController::class, 'update'])->name('team-members.update');
                    Route::delete('/teams/{team}/members/{user}', [TeamMemberController::class, 'destroy'])->name('team-members.destroy');

                    Route::get('/team-invitations/{invitation}', [TeamInvitationController::class, 'accept'])
                        ->middleware(['signed'])
                        ->name('team-invitations.accept');

                    Route::delete('/team-invitations/{invitation}', [TeamInvitationController::class, 'destroy'])
                        ->name('team-invitations.destroy');
                }

                Route::get('app/settings', [AppSettingsController::class, 'index'])->name('app.settings.index');
                Route::post('app/settings/logo', [LogoController::class, 'store'])->name('app.settings.logo');
            });
        });
    });
});
