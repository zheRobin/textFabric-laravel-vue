<?php

use Illuminate\Support\Facades\Route;
use Modules\Imports\Controllers\ImportController;

// TODO: remove temporary route

// TODO: add middlewares
Route::get('import', [ImportController::class, 'index'])->name('import.index');
Route::post('import/file', [ImportController::class, 'store'])->name('import.file');
Route::post('import/images', [ImportController::class, 'importImages'])->name('import.images');
