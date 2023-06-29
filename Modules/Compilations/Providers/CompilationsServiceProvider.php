<?php

namespace Modules\Compilations\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CompilationsServiceProvider extends ServiceProvider
{
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
}
