<?php

use Illuminate\Support\Facades\Route;
use Modules\Imports\Controllers\CollectionHeaderController;
use Modules\Imports\Controllers\CollectionItemController;
use Modules\Imports\Controllers\ImportController;

Route::group(['prefix' => LaravelLocalization::setLocale()], function (){
    $authMiddleware = array_filter([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'subscribed'
    ]);
    Route::middleware($authMiddleware)->group(function () {
        Route::get('import', [ImportController::class, 'index'])->name('import.index');
        Route::post('import/file', [ImportController::class, 'store'])->name('import.file');
        Route::post('import/images', [ImportController::class, 'importImages'])->name('import.images');

        Route::put('collection-items/{collectionItem}', [CollectionItemController::class, 'update'])->name('collection-items.update');
        Route::post('collection-items/{collectionItem}/image', [CollectionItemController::class, 'updateImage'])->name('collection-items.update-image');

        Route::put('collections/{collection}/update-header', [CollectionHeaderController::class, 'update'])->name('collections.update-header');
    });
});

