<?php

use Illuminate\Support\Facades\Route;
use Modules\OpenAI\Controllers\OpenAIController;

$authMiddleware = array_filter([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'subscribed'
]);

Route::middleware($authMiddleware)->group(function () {
    // dummy route
   Route::get('openai', [OpenAIController::class, 'index']);
});
