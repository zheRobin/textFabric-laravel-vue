<?php

use Illuminate\Support\Facades\Route;
use Modules\OpenAI\Controllers\CollectionItemCompletionController;
use Modules\OpenAI\Controllers\OpenAIController;

Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
    $authMiddleware = array_filter([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'subscribed',
    ]);
    Route::middleware($authMiddleware)->group(function () {
        // dummy route
        Route::get('openai', [OpenAIController::class, 'index'])->name('openai.index');

        Route::post('/update/presets', [OpenAIController::class, 'update'])->name('openai.update');
        Route::post('/delete/presets', [OpenAIController::class, 'delete'])->name('openai.delete');

        Route::get('openai/presets/{preset}/complete-item/{item}', [CollectionItemCompletionController::class, 'complete'])->name('openai.item-completion');
    });
});

