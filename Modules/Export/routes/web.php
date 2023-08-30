<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Export\Controllers\ExportCollectionItemController;
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
            Route::post('/export/generate/compilations/{compilation}', [ExportController::class, 'generate'])->name('export.generate');
            Route::post('/export/{export}/delete', [ExportController::class, 'delete'])->name('export.delete');
            Route::post('/export/translate/{export}', [ExportController::class, 'translate'])->name('export.translation');

            Route::get('/export/{export}/items', [ExportCollectionItemController::class, 'index'])->name('export.items.index');

            Route::post('/export/{export}/download', [ExportController::class, 'download'])->name('export.download');
            Route::post('/export', [ExportController::class, 'search'])->name('export.search');

            Route::get('/export/showProgress', [ExportController::class, 'showProgress'])->name('export.showProgress');

            Route::get('/export/cancel/{id}', [ExportController::class, 'cancel']);
        });
});
