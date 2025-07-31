<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\CashFlow;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $reportType = $request->get('report_type', 'sales');

        // Summary stats
        $stats = $this->getReportStats($dateFrom, $dateTo);

        // Chart data
        $chartData = $this->getChartData($dateFrom, $dateTo, $reportType);

        return view('reports.index', compact('stats', 'chartData', 'dateFrom', 'dateTo', 'reportType'));
    }

    public function sales(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $perPage = $request->get('per_page', 20);

        $transactions = Transaction::with(['details.product', 'details.unit'])
            ->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->orderBy('transaction_date', 'desc')
            ->paginate($perPage);

        $summary = $this->getSalesSummary($dateFrom, $dateTo);
        $topProducts = $this->getTopSellingProducts($dateFrom, $dateTo);

        return view('reports.sales', compact('transactions', 'summary', 'topProducts', 'dateFrom', 'dateTo'));
    }

    public function stock(Request $request)
    {
        $category = $request->get('category');
        $stockStatus = $request->get('stock_status', 'all');

        $query = Product::with(['category', 'stocks.unit', 'productUnits.unit']);

        if ($category) {
            $query->where('category_id', $category);
        }

        $products = $query->orderBy('name')->get();

        // Filter by stock status
        if ($stockStatus !== 'all') {
            $products = $products->filter(function ($product) use ($stockStatus) {
                $isLowStock = $product->isLowStock();
                return $stockStatus === 'low' ? $isLowStock : !$isLowStock;
            });
        }

        $categories = \App\Models\Category::orderBy('name')->get();
        $stockValue = $this->calculateStockValue($products);

        return view('reports.stock', compact('products', 'categories', 'stockValue', 'category', 'stockStatus'));
    }

    public function cashflow(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $type = $request->get('type', 'all');

        $query = CashFlow::whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $cashFlows = $query->orderBy('transaction_date', 'desc')->paginate(20);

        $summary = $this->getCashFlowSummary($dateFrom, $dateTo);
        $categoryBreakdown = $this->getCashFlowByCategory($dateFrom, $dateTo);

        return view('reports.cashflow', compact('cashFlows', 'summary', 'categoryBreakdown', 'dateFrom', 'dateTo', 'type'));
    }

    public function viewTransaction(Transaction $transaction)
    {
        $transaction->load(['details.product', 'details.unit']);
        return view('reports.transaction-detail', compact('transaction'));
    }

    public function editTransaction(Transaction $transaction)
    {
        $transaction->load(['details.product.productUnits.unit', 'details.unit']);
        $products = Product::with(['productUnits.unit'])->where('is_active', true)->get();
        
        return view('reports.edit-transaction', compact('transaction', 'products'));
    }

    public function updateTransaction(Request $request, Transaction $transaction)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $transaction) {
                // Delete old transaction details
                $transaction->details()->delete();

                $subtotal = 0;

                // Create new transaction details
                foreach ($request->items as $item) {
                    $itemSubtotal = $item['quantity'] * $item['unit_price'];
                    $subtotal += $itemSubtotal;

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $item['product_id'],
                        'unit_id' => $item['unit_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $itemSubtotal,
                    ]);
                }

                // Update transaction totals
                $transaction->update([
                    'subtotal' => $subtotal,
                    'total_amount' => $subtotal, // Assuming no tax for now
                    'paid_amount' => $request->paid_amount,
                    'change_amount' => $request->paid_amount - $subtotal,
                ]);

                // Update cash flow
                $cashFlow = CashFlow::where('reference_type', 'transaction')
                    ->where('reference_id', $transaction->id)
                    ->first();

                if ($cashFlow) {
                    $cashFlow->update([
                        'amount' => $subtotal,
                        'description' => 'Penjualan - ' . $transaction->transaction_number . ' (Diperbarui)',
                    ]);
                }
            });

            return redirect()->route('reports.sales')
                ->with('success', 'Transaksi berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui transaksi: ' . $e->getMessage()]);
        }
    }

    public function exportSales(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $format = $request->get('format', 'pdf');

        $transactions = Transaction::with(['details.product', 'details.unit'])
            ->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $summary = $this->getSalesSummary($dateFrom, $dateTo);

        if ($format === 'pdf') {
            $pdf = PDF::loadView('reports.exports.sales-pdf', compact('transactions', 'summary', 'dateFrom', 'dateTo'));
            return $pdf->download('laporan-penjualan-' . $dateFrom . '-to-' . $dateTo . '.pdf');
        }

        // Excel export would go here
        return back()->with('error', 'Format export tidak didukung');
    }

    public function exportStock(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $products = Product::with(['category', 'stocks.unit', 'productUnits.unit'])->get();
        $stockValue = $this->calculateStockValue($products);

        if ($format === 'pdf') {
            $pdf = PDF::loadView('reports.exports.stock-pdf', compact('products', 'stockValue'));
            return $pdf->download('laporan-stok-' . now()->format('Y-m-d') . '.pdf');
        }

        return back()->with('error', 'Format export tidak didukung');
    }

    private function getReportStats($dateFrom, $dateTo)
    {
        $transactions = Transaction::whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
        
        $totalSales = $transactions->sum('total_amount');
        $totalTransactions = $transactions->count();
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        $cashFlow = CashFlow::whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
        $totalIncome = $cashFlow->where('type', 'income')->sum('amount');
        $totalExpense = $cashFlow->where('type', 'expense')->sum('amount');
        $netIncome = $totalIncome - $totalExpense;

        return [
            'total_sales' => $totalSales,
            'total_transactions' => $totalTransactions,
            'average_transaction' => $averageTransaction,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_income' => $netIncome,
        ];
    }

    private function getChartData($dateFrom, $dateTo, $type)
    {
        $startDate = Carbon::parse($dateFrom);
        $endDate = Carbon::parse($dateTo);

        $data = [];
        
        if ($type === 'sales') {
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dayTotal = Transaction::whereDate('transaction_date', $date)
                    ->sum('total_amount');
                
                $data[] = [
                    'date' => $date->format('Y-m-d'),
                    'formatted_date' => $date->format('d/m'),
                    'value' => (float) $dayTotal,
                ];
            }
        }

        return $data;
    }

    private function getSalesSummary($dateFrom, $dateTo)
    {
        return [
            'total_revenue' => Transaction::whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->sum('total_amount'),
            'total_transactions' => Transaction::whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->count(),
            'total_items_sold' => TransactionDetail::whereHas('transaction', function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            })->sum('quantity'),
        ];
    }

    private function getTopSellingProducts($dateFrom, $dateTo, $limit = 10)
    {
        return TransactionDetail::select(
                'product_id',
                'products.name as product_name',
                'units.symbol as unit_symbol',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT transaction_id) as transaction_count')
            )
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('units', 'transaction_details.unit_id', '=', 'units.id')
            ->whereHas('transaction', function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            })
            ->groupBy('product_id', 'products.name', 'units.symbol')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }

    private function calculateStockValue($products)
    {
        $totalValue = 0;
        $totalProducts = $products->count();
        $lowStockCount = 0;

        foreach ($products as $product) {
            if ($product->isLowStock()) {
                $lowStockCount++;
            }

            foreach ($product->stocks as $stock) {
                $productUnit = $product->productUnits->where('unit_id', $stock->unit_id)->first();
                if ($productUnit) {
                    $totalValue += $stock->quantity * $productUnit->price;
                }
            }
        }

        return [
            'total_value' => $totalValue,
            'total_products' => $totalProducts,
            'low_stock_count' => $lowStockCount,
        ];
    }

    private function getCashFlowSummary($dateFrom, $dateTo)
    {
        $income = CashFlow::whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('type', 'income')
            ->sum('amount');

        $expense = CashFlow::whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('type', 'expense')
            ->sum('amount');

        return [
            'total_income' => $income,
            'total_expense' => $expense,
            'net_flow' => $income - $expense,
        ];
    }

    private function getCashFlowByCategory($dateFrom, $dateTo)
    {
        return CashFlow::select('category', 'type', DB::raw('SUM(amount) as total'))
            ->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy('category', 'type')
            ->orderBy('total', 'desc')
            ->get()
            ->groupBy('type');
    }
}