<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POSController;
use App\Http\Controllers\Api\APIPOSController;



Route::prefix('pos')->group(function () {
    // Search products
    Route::get('/search-product', [POSController::class, 'searchProduct'])->name('api.pos.search-product');
    
    // Get product by barcode
    Route::get('/product-by-barcode', [POSController::class, 'getProductByBarcode'])->name('api.pos.product-by-barcode');
    
    // Calculate tiered price
    Route::post('/calculate-tiered-price', [POSController::class, 'calculateTieredPrice'])->name('api.pos.calculate-tiered-price');
    
    // Process transaction
    Route::post('/process', [POSController::class, 'processTransaction'])->name('api.pos.process');
});
