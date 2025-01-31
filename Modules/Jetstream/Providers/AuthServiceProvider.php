<?php

namespace Modules\Jetstream\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Jetstream\Auth\ApiFeaturesUserAbilities;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('use-api-features', [new ApiFeaturesUserAbilities, 'useApiFeatures']);
        Gate::define('update-logo', fn (User $user) => $user->isSuperAdmin());
    }
}
