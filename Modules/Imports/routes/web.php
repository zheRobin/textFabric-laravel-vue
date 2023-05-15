<?php

use Illuminate\Support\Facades\Route;
use Modules\Imports\Controllers\ImportController;

// TODO: remove temporary route
Route::post('import/test', [ImportController::class, 'index']);
Route::post('import/images', [ImportController::class, 'importImages']);
