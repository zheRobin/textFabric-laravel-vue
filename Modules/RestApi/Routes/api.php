<?php

use Illuminate\Support\Facades\Route;
use Modules\RestApi\Controllers\RestApiController;
use Modules\OpenAI\Controllers\CollectionItemCompletionController;

Route::get('/', [RestApiController::class, 'index']);

Route::middleware([
    'auth:sanctum',
    'throttle:rest_api_requests',
    'token.enabled'
])->group(function () {
    Route::post('generate', [RestApiController::class, 'generate'])->name('restApi.generate');
    Route::post('translate', [RestApiController::class, 'translate'])->name('restApi.translate');
});
