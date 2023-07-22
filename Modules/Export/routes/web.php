<?php

use Illuminate\Support\Facades\Route;
use Modules\Compilations\Controllers\CompilationsController;
use Modules\Compilations\Providers\LocalizationProvider;
use App\Services\GetLocaleService;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Export\Controllers\ExportController;
$authMiddleware = array_filter([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'subscribed'
]);

Route::middleware($authMiddleware)->group(function () {
    Route::group(['prefix' => LaravelLocalization::setLocale()]
        , function () {
            Route::get('/export', [ExportController::class, 'index'])->name('export.index');
            Route::post('/export/generate', [ExportController::class, 'generate'])->name('export.generate');
            Route::post('/export/delete', [ExportController::class, 'delete'])->name('export.delete');
            Route::post('/export/translation', [ExportController::class, 'translation'])->name('export.translation');
        });
});
