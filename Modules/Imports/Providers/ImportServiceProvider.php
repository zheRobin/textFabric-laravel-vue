<?php

namespace Modules\Imports\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Imports\Actions\ImportImage;
use Modules\Imports\Actions\ImportImagesFromExcel;
use Modules\Imports\Actions\StoreImportingFile;
use Modules\Imports\Contracts\ImportsImage;
use Modules\Imports\Contracts\ImportsImagesFromExcel;
use Modules\Imports\Contracts\StoresImportingFile;

class ImportServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        StoresImportingFile::class => StoreImportingFile::class,
        ImportsImagesFromExcel::class => ImportImagesFromExcel::class,
        ImportsImage::class => ImportImage::class,
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
