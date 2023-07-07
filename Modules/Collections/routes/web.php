<?php

use Illuminate\Support\Facades\Route;
use Modules\Collections\Controllers\CollectionController;
use Modules\Collections\Controllers\CurrentCollectionController;

$authMiddleware = array_filter([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'subscribed'
]);

Route::middleware($authMiddleware)->group(function () {
    Route::resource('collections', CollectionController::class);

    Route::get('/current-collection/items/iterate', [CurrentCollectionController::class, 'itemsIteration'])->name('current-collection.items.iterate');
    Route::put('/current-collection/{collection}', [CurrentCollectionController::class, 'update'])->name('current-collection.update');
});
