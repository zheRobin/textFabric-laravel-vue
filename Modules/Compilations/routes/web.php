<?php

use Illuminate\Support\Facades\Route;
use Modules\Compilations\Controllers\CompilationsController;
use Modules\Compilations\Providers\LocalizationProvider;
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
        Route::get('/compilations', [CompilationsController::class, 'index'])->name('compilations.index');
        Route::patch('/compilations/update', [CompilationsController::class, 'update'])->name('compilations.update');
        Route::patch('/compilations/store', [CompilationsController::class, 'store'])->name('compilations.store');

        Route::delete('/compilations/delete', [CompilationsController::class, 'delete'])->name('compilations.delete');
    });
});
