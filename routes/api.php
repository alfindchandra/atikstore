<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\APIPOSController;

Route::prefix('pos')->name('api.pos.')->group(function () {
    Route::get('/search-product', [APIPOSController::class, 'searchProduct'])->name('search-product');
    Route::get('/product-by-barcode', [APIPOSController::class, 'getProductByBarcode'])->name('product-by-barcode');
    Route::post('/process', [APIPOSController::class, 'processTransaction'])->name('process');
    Route::get('/today-transactions', [APIPOSController::class, 'getTodayTransactions'])->name('today-transactions');
    Route::get('/stats', [APIPOSController::class, 'getStats'])->name('stats');
});