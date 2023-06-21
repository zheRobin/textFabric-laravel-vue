<?php

use Illuminate\Support\Facades\Route;
use Modules\Presets\Controllers\PresetController;

$authMiddleware = array_filter([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'subscribed'
]);

Route::middleware($authMiddleware)->group(function () {
    Route::resource('presets', PresetController::class)->except(['index']);
});
