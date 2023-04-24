<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscriptions\Controllers\PlanSubscriptionController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::put('plan-subscriptions/{plan}', [PlanSubscriptionController::class, 'update'])->name('plan-subscriptions.update');
});
