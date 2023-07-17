<?php

namespace Modules\RestApi\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\OpenAI\Actions\BuildPrompt;
use Modules\OpenAI\Contracts\BuildsPrompt;
use Modules\RestApi\Actions\CompleteCollectionItem;
use Modules\RestApi\Contracts\CompletesCollectionItem;
class RestApiServiceProvider extends ServiceProvider
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

    public function register()
    {
        Route::prefix('api/rest')
            ->middleware(['api'])
            ->group(function () {
                $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
            });

        $this->mergeConfigFrom(__DIR__.'/../Config/rest-api.php', 'rest-api');
    }

    public function boot()
    {
        RateLimiter::for('rest_api_requests', function () {
            return Limit::perMinute(config('rest-api.API_REQUEST_LIMIT'));
        });
    }
}
