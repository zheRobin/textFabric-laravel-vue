<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\Controllers\ApiController;
use App\Services\GetLocaleService;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

$authMiddleware = array_filter([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'subscribed'
]);

Route::middleware($authMiddleware)->group(function () {
    Route::group(['prefix' => LaravelLocalization::setLocale()]
        , function () {
        Route::get('/api', [ApiController::class, 'index'])->name('api.index');

        Route::post('/create', [ApiController::class, 'create'])->name('api.create');
    });
});
