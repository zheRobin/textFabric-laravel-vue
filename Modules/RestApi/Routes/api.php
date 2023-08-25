<?php

use Illuminate\Support\Facades\Route;
use Modules\RestApi\Controllers\RestApiController;

Route::middleware([
    'auth:sanctum',
    'token.enabled',
    'throttle:rest_api_requests'
])->group(function () {
    Route::post('generate', [RestApiController::class, 'generate'])->name('restApi.generate');
    Route::post('translate', [RestApiController::class, 'translate'])->name('restApi.translate');
});


