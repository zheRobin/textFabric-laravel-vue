<?php

namespace Modules\Subscriptions\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Subscriptions\Actions\CreatePlan;
use Modules\Subscriptions\Actions\UpdatePlanSubscription;
use Modules\Subscriptions\Contracts\CreatesPlan;
use Modules\Subscriptions\Contracts\UpdatesPlanSubscription;
use Modules\Subscriptions\Models\PlanSubscription;
use Modules\Subscriptions\Policies\PlanSubscriptionPolicy;

class SubscriptionsServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        CreatesPlan::class => CreatePlan::class,
        UpdatesPlanSubscription::class => UpdatePlanSubscription::class,
    ];

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PlanSubscription::class => PlanSubscriptionPolicy::class,
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
        $this->registerPolicies();
    }

    /**
     * Configure routes.
     */
    protected function configureRoutes(): void
    {
        Route::middleware('web')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });
    }

    /**
     * Configure publishing.
     */
    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
