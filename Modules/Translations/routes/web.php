<?php

use Illuminate\Support\Facades\Route;
use Modules\Translations\Controllers\CollectionItemTranslationController;
use Modules\Translations\Controllers\TextTranslationController;

Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
    $authMiddleware = array_filter([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'subscribed'
    ]);

    Route::middleware($authMiddleware)->group(function () {
        Route::post('deepl/translate', [TextTranslationController::class, 'index'])->name('deepl.translate-text');

        Route::post('deepl/collection-items/{item}/translate', [CollectionItemTranslationController::class, 'index'])->name('deepl.translate-collection-item');
    });
});
