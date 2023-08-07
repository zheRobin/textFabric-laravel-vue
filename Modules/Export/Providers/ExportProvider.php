<?php

namespace Modules\Export\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\OpenAI\Actions\BuildPrompt;
use Modules\Export\Contracts\BuildsPrompt;
use Modules\Export\Actions\CompleteCollectionItem;
use Modules\Export\Contracts\CompletesCollectionItem;
use Illuminate\Queue\Events\JobProcessed;
use Modules\Export\Listeners\AfterJobProcessedListener;
class ExportProvider extends ServiceProvider
{

    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        CompletesCollectionItem::class => CompleteCollectionItem::class,
        BuildsPrompt::class => BuildPrompt::class,
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
