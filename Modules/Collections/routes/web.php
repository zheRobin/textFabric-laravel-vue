<?php

use Illuminate\Support\Facades\Route;
use Modules\Collections\Controllers\CollectionController;

$authMiddleware = array_filter([
    'auth:sanctum',
    config('jetstream.auth_session'),
]);

Route::middleware($authMiddleware)->resource('collections', CollectionController::class);
