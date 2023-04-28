<?php

namespace Modules\Collections\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Collections\Actions\CreateCollection;
use Modules\Collections\Actions\DeleteCollection;
use Modules\Collections\Actions\UpdateCollection;
use Modules\Collections\Contracts\CreatesCollection;
use Modules\Collections\Contracts\DeletesCollection;
use Modules\Collections\Contracts\UpdatesCollection;
use Modules\Collections\Models\Collection;
use Modules\Collections\Policies\CollectionPolicy;

class CollectionsServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        CreatesCollection::class => CreateCollection::class,
        DeletesCollection::class => DeleteCollection::class,
        UpdatesCollection::class => UpdateCollection::class,
    ];

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Collection::class => CollectionPolicy::class,
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
