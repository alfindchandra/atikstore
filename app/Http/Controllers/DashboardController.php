<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\StockService;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected ReportService $reportService;
    protected StockService $stockService;
    protected TransactionService $transactionService;

    public function __construct(
        ReportService $reportService,
        StockService $stockService,
        TransactionService $transactionService
    ) {
        $this->reportService = $reportService;
        $this->stockService = $stockService;
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        $stats = $this->reportService->getDashboardStats();
        $topProducts = $this->reportService->getTopSellingProducts(7, 5);
        $salesChart = $this->reportService->getSalesChart(7);
        $recentTransactions = $this->transactionService->getTodayTransactions()->take(5);
        $lowStockProducts = $this->stockService->getLowStockProducts();

        return view('dashboard', compact(
            'stats',
            'topProducts', 
            'salesChart',
            'recentTransactions',
            'lowStockProducts'
        ));
    }
}