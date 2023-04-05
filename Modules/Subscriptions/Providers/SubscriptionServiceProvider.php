<?php

namespace Modules\Subscriptions\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Subscriptions\Actions\CreatePlan;
use Modules\Subscriptions\Contracts\CreatesPlan;

class SubscriptionServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        CreatesPlan::class => CreatePlan::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureRoutes();
        $this->registerMigrations();
    }

    /**
     * Configure routes.
     */
    protected function configureRoutes(): void
    {

    }

    /**
     * Configure publishing.
     */
    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
