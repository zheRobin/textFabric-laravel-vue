<?php

namespace Modules\Imports\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Imports\Actions\ImportImage;
use Modules\Imports\Actions\ImportImagesFromExcel;
use Modules\Imports\Actions\StoreImportingFile;
use Modules\Imports\Actions\UpdateCollectionHeader;
use Modules\Imports\Actions\UpdateCollectionItem;
use Modules\Imports\Actions\UpdateImageInCell;
use Modules\Imports\Contracts\ImportsImage;
use Modules\Imports\Contracts\ImportsImagesFromExcel;
use Modules\Imports\Contracts\StoresImportingFile;
use Modules\Imports\Contracts\UpdatesCollectionHeader;
use Modules\Imports\Contracts\UpdatesCollectionItem;
use Modules\Imports\Contracts\UpdatesImageInCell;
use Modules\Imports\Models\CollectionItem;
use Modules\Imports\Policies\CollectionItemPolicy;

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
        UpdatesCollectionItem::class => UpdateCollectionItem::class,
        UpdatesImageInCell::class => UpdateImageInCell::class,
        UpdatesCollectionHeader::class => UpdateCollectionHeader::class,
    ];

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        CollectionItem::class => CollectionItemPolicy::class,
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
