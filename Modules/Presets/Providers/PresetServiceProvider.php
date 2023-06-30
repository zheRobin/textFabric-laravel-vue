<?php

namespace Modules\Presets\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Presets\Actions\CreatePreset;
use Modules\Presets\Actions\DeletePreset;
use Modules\Presets\Actions\UpdatePreset;
use Modules\Presets\Actions\ValidatePreset;
use Modules\Presets\Contracts\CreatesPreset;
use Modules\Presets\Contracts\DeletesPreset;
use Modules\Presets\Contracts\UpdatesPreset;
use Modules\Presets\Contracts\ValidatesPreset;
use Modules\Presets\Models\Preset;
use Modules\Presets\Policies\PresetPolicy;

class PresetServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        ValidatesPreset::class => ValidatePreset::class,
        CreatesPreset::class => CreatePreset::class,
        UpdatesPreset::class => UpdatePreset::class,
        DeletesPreset::class => DeletePreset::class,
    ];

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Preset::class => PresetPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {

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
