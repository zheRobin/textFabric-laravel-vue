<?php

namespace Modules\Translations\Providers;

use DeepL\Translator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Translations\Actions\TranslateCollectionItemData;
use Modules\Translations\Actions\TranslateData;
use Modules\Translations\Actions\UpdateLanguages;
use Modules\Translations\Contracts\TranslatesCollectionItemData;
use Modules\Translations\Contracts\TranslatesData;
use Modules\Translations\Contracts\UpdatesLanguages;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        UpdatesLanguages::class => UpdateLanguages::class,
        TranslatesCollectionItemData::class => TranslateCollectionItemData::class,
        TranslatesData::class => TranslateData::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Translator::class, function (Application $app) {
            return new Translator(
                authKey: config('deepl.api_key')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerMigrations();
        $this->mergeConfigFrom(__DIR__ . '/../Config/deepl.php', 'deepl');
    }

    /**
     * Configure publishing.
     */
    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
