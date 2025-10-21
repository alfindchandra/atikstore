<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ActionsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;

// Rute yang dapat diakses oleh semua orang (tanpa login)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'loginvalidate'])->name('login.validate');
    
    // Redirect root ke login jika belum login
    Route::get('/', function () {
        return redirect('/login');
    });
});
// Rute yang dilindungi oleh middleware 'auth' (hanya bisa diakses setelah login)
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // POS Routes
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [POSController::class, 'index'])->name('index');
        Route::get('/receipt/{transaction}', [POSController::class, 'receipt'])->name('receipt');
        Route::get('/receipt/{transaction}/print', [POSController::class, 'printReceipt'])->name('print-receipt');
    });

    // Product Routes
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');

    // Category Routes
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('units', UnitController::class)->except(['show']);

    // debt customers
    Route::prefix('debts')->name('debts.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{debt}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{debt}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{debt}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{debt}', [CustomerController::class, 'destroy'])->name('destroy');
        
        // Payment routes
        Route::post('/{debt}/payment', [CustomerController::class, 'addPayment'])->name('add-payment');
        
        // API routes
        Route::get('/api/transaction-items', [CustomerController::class, 'getTransactionItems'])->name('transaction-items');
    });

    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::resource('/', SupplierController::class);
        Route::post('/{supplier}/toggle', [SupplierController::class, 'toggle'])->name('suppliers.toggle');
    });

    // Purchase routes
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::resource('/', PurchaseController::class);
        Route::get('/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
        Route::get('/{purchase}/show', [PurchaseController::class, 'show'])->name('purchases.show');
        Route::get('/{purchase}/receipt', [PurchaseController::class, 'receipt'])->name('purchases.receipt');
        Route::get('/{purchase}/download-receipt', [PurchaseController::class, 'downloadReceipt'])->name('purchases.download-receipt');
        Route::get('/{purchase}/receipt-data', [PurchaseController::class, 'getReceiptData'])->name('purchases.receipt-data');
    });
    
    // Stock Routes
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::get('/adjustment', [StockController::class, 'adjustment'])->name('adjustment');
        Route::post('/adjustment', [StockController::class, 'processAdjustment'])->name('process-adjustment');
        Route::get('/movement', [StockController::class, 'movement'])->name('movement');
        Route::get('/product/{product}', [StockController::class, 'productStock'])->name('product');
        Route::get('/{product}/edit', [StockController::class, 'edit'])->name('edit');
        Route::put('/{product}', [StockController::class, 'update'])->name('update');
    });

    // Cash Flow Routes
    Route::prefix('cashflow')->name('cashflow.')->group(function () {
        Route::get('/', [CashFlowController::class, 'index'])->name('index');
        Route::get('/create', [CashFlowController::class, 'create'])->name('create');
        Route::post('/', [CashFlowController::class, 'store'])->name('store');
        Route::get('/{cashFlow}/edit', [CashFlowController::class, 'edit'])->name('edit');
        Route::patch('/{cashFlow}', [CashFlowController::class, 'update'])->name('update');
        Route::delete('/{cashFlow}', [CashFlowController::class, 'destroy'])->name('destroy');
    });

    // Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('/cashflow', [ReportController::class, 'cashflow'])->name('cashflow');
        
        // Transaction management routes
        Route::get('/transactions/{transaction}', [ReportController::class, 'viewTransaction'])->name('transactions.view');
        Route::get('/transactions/{transaction}/edit', [ReportController::class, 'editTransaction'])->name('transactions.edit');
        Route::put('/transactions/{transaction}', [ReportController::class, 'updateTransaction'])->name('transactions.update');
        
        // Export routes
        Route::get('/export/sales', [ReportController::class, 'exportSales'])->name('export.sales');
        Route::get('/export/stock', [ReportController::class, 'exportStock'])->name('export.stock');
    });

    // actions
    Route::prefix('actions')->name('actions.')->group(function () {
        Route::get('/',[ActionsController::class,'index'])->name('index');
    });

    // API Routes for AJAX calls
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/products/search', [ProductController::class, 'apiSearch'])->name('products.search');
        Route::get('/products/{product}/units', [ProductController::class, 'getUnits'])->name('products.units');
        Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    });
});
