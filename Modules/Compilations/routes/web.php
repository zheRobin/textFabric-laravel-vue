<?php

use Illuminate\Support\Facades\Route;
use Modules\Compilations\Controllers\CompilationsController;
use Modules\Compilations\Providers\LocalizationProvider;
use App\Services\GetLocaleService;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
    $authMiddleware = array_filter([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'subscribed'
    ]);

    Route::middleware($authMiddleware)->group(function () {
            Route::get('/compilations', [CompilationsController::class, 'index'])->name('compilations.index');
            Route::patch('/compilations/update', [CompilationsController::class, 'update'])->name('compilations.update');
            Route::patch('/compilations/store', [CompilationsController::class, 'store'])->name('compilations.store');

            Route::delete('/compilations/delete', [CompilationsController::class, 'delete'])->name('compilations.delete');
            Route::get('/compilations/get-item/{id}', [CompilationsController::class, 'getItem'])->name('compilations.get-item');
    });

    Route::middleware(['guest'])->group(function () {
        Route::get('demo/compilations', function () {
            return \Inertia\Inertia::render('Compilations::Demo/Index', [

            ]);
        })->name('demo.compilations.index');
    });
});
