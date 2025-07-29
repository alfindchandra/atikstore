<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\APIPOSController;

Route::prefix('pos')->group(function () {
    Route::get('/search-product', [APIPOSController::class, 'searchProduct']);
    Route::post('/process', [APIPOSController::class, 'processTransaction']);
});
