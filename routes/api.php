<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\RetailerController;

// If you use Sanctum:
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/search/isbn', [SearchController::class, 'byIsbn']);
    Route::get('/search/title', [SearchController::class, 'byTitle']);

    Route::post('/import/file', [ImportController::class, 'importFile']);
//    Route::post('/import/goodreads', [ImportController::class, 'importGoodreads']);
    Route::get('/imports/{import}', [ImportController::class, 'show']);

    Route::get('/retailers', [RetailerController::class, 'index']);
});
