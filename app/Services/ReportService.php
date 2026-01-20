<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\CashFlow;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDashboardStats()
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();

        // Today's stats
        $todaySales = Transaction::whereDate('transaction_date', $today)->sum('total_amount');
        $todayTransactions = Transaction::whereDate('transaction_date', $today)->count();
        
        // Monthly stats
        $monthlySales = Transaction::whereBetween('transaction_date', [$thisMonth, now()])->sum('total_amount');
        
        // Low stock products
        $lowStockProducts = $this->getLowStockProductsCount();
        
        // Today's cash flow
        $todayIncome = CashFlow::whereDate('transaction_date', $today)
            ->where('type', 'income')
            ->sum('amount');
            
        $todayExpense = CashFlow::whereDate('transaction_date', $today)
            ->where('type', 'expense')
            ->sum('amount');
            
        $todayNet = $todayIncome - $todayExpense;

        return [
            'today_sales' => $todaySales,
            'today_transactions' => $todayTransactions,
            'monthly_sales' => $monthlySales,
            'low_stock_products' => $lowStockProducts,
            'today_income' => $todayIncome,
            'today_expense' => $todayExpense,
            'today_net' => $todayNet,
        ];
    }

public function getTopSellingProducts($startDate = null, $endDate = null, $limit = 5)
    {
        // If no start date is provided, default to 7 days ago
        if (is_null($startDate)) {
            $startDate = Carbon::now()->subDays(7)->startOfDay();
        } else {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }

        // If no end date is provided, default to today
        if (is_null($endDate)) {
            $endDate = Carbon::now()->endOfDay();
        } else {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        $topProducts = TransactionDetail::query()
            ->select(
                'product_id',
                'products.name as product_name',
                'units.symbol as unit_symbol',
                \DB::raw('SUM(transaction_details.quantity) as total_quantity'), // Specify table for quantity
                \DB::raw('SUM(transaction_details.subtotal) as total_revenue'),   // Specify table for subtotal
                \DB::raw('COUNT(DISTINCT transaction_id) as transaction_count')
            )
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('units', 'transaction_details.unit_id', '=', 'units.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
            ->groupBy('product_id', 'products.name', 'units.symbol')
            ->orderByDesc('total_revenue') // Use the alias defined in the select
            ->limit($limit)
            ->get();

        return $topProducts;
    }


    public function getSalesChart($days = 7)
    {
        $data = [];
        $startDate = now()->subDays($days - 1)->startOfDay();
        
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $total = Transaction::whereDate('transaction_date', $date)->sum('total_amount');
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'formatted_date' => $date->format('d/m'),
                'total' => (float) $total,
            ];
        }
        
        return $data;
    }

    public function getLowStockProductsCount()
    {
        return Product::whereRaw('
            (SELECT COALESCE(SUM(
                stocks.quantity * COALESCE(product_units.conversion_rate, 1)
            ), 0) 
            FROM stocks 
            LEFT JOIN product_units ON stocks.unit_id = product_units.unit_id 
                AND stocks.product_id = product_units.product_id
            WHERE stocks.product_id = products.id) <= products.stock_alert_minimum
        ')->count();
    }

    public function getRevenueByPeriod($startDate, $endDate, $groupBy = 'day')
    {
        $query = Transaction::whereBetween('transaction_date', [$startDate, $endDate]);
        
        switch ($groupBy) {
            case 'day':
                return $query->selectRaw('DATE(transaction_date) as period, SUM(total_amount) as total')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                    
            case 'week':
                return $query->selectRaw('YEARWEEK(transaction_date) as period, SUM(total_amount) as total')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                    
            case 'month':
                return $query->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as period, SUM(total_amount) as total')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                    
            default:
                return collect();
        }
    }

    public function getProductPerformance($startDate, $endDate)
    {
        return TransactionDetail::select(
                'products.name',
                'categories.name as category_name',
                DB::raw('SUM(transaction_details.quantity) as total_sold'),
                DB::raw('SUM(transaction_details.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT transaction_details.transaction_id) as transaction_count'),
                DB::raw('AVG(transaction_details.unit_price) as avg_price')
            )
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();
    }

    public function getCashFlowAnalysis($startDate, $endDate)
    {
        $income = CashFlow::whereBetween('transaction_date', [$startDate, $endDate])
            ->where('type', 'income')
            ->sum('amount');
            
        $expense = CashFlow::whereBetween('transaction_date', [$startDate, $endDate])
            ->where('type', 'expense')
            ->sum('amount');
            
        $netFlow = $income - $expense;
        
        // Category breakdown
        $incomeByCategory = CashFlow::select('category', DB::raw('SUM(amount) as total'))
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('type', 'income')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
            
        $expenseByCategory = CashFlow::select('category', DB::raw('SUM(amount) as total'))
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('type', 'expense')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return [
            'total_income' => $income,
            'total_expense' => $expense,
            'net_flow' => $netFlow,
            'income_by_category' => $incomeByCategory,
            'expense_by_category' => $expenseByCategory,
        ];
    }

    public function getInventoryValue()
    {
        $totalValue = 0;
        $products = Product::with(['stocks', 'productUnits'])->get();
        
        foreach ($products as $product) {
            foreach ($product->stocks as $stock) {
                $productUnit = $product->productUnits->where('unit_id', $stock->unit_id)->first();
                if ($productUnit) {
                    $totalValue += $stock->quantity * $productUnit->price;
                }
            }
        }
        
        return $totalValue;
    }

    public function getLowStockProducts()
    {
        return Product::with(['category', 'stocks.unit', 'productUnits.unit'])
            ->whereRaw('
                (SELECT COALESCE(SUM(
                    stocks.quantity * COALESCE(product_units.conversion_rate, 1)
                ), 0) 
                FROM stocks 
                LEFT JOIN product_units ON stocks.unit_id = product_units.unit_id 
                    AND stocks.product_id = product_units.product_id
                WHERE stocks.product_id = products.id) <= products.stock_alert_minimum
            ')
            ->get()
            ->map(function ($product) {
                $baseUnit = $product->getBaseUnit();
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name ?? 'Tidak ada kategori',
                    'current_stock' => $product->getTotalStockInBaseUnit(),
                    'minimum_stock' => $product->stock_alert_minimum,
                    'base_unit' => $baseUnit ? $baseUnit->unit->symbol : 'unit',
                ];
            });
    }

    public function getCustomerInsights($startDate, $endDate)
    {
        // Average transaction value
        $avgTransactionValue = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->avg('total_amount');
            
        // Peak hours analysis
        $peakHours = Transaction::selectRaw('HOUR(transaction_date) as hour, COUNT(*) as transaction_count')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('hour')
            ->orderByDesc('transaction_count')
            ->limit(5)
            ->get();
            
        // Peak days analysis
        $peakDays = Transaction::selectRaw('DAYNAME(transaction_date) as day, COUNT(*) as transaction_count, SUM(total_amount) as total_sales')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('day')
            ->orderByDesc('transaction_count')
            ->get();

        return [
            'avg_transaction_value' => $avgTransactionValue,
            'peak_hours' => $peakHours,
            'peak_days' => $peakDays,
        ];
    }

    public function getProfitMarginAnalysis($startDate, $endDate)
    {
        // This would require cost price data in the database
        // For now, we'll return a placeholder structure
        
        return [
            'total_revenue' => Transaction::whereBetween('transaction_date', [$startDate, $endDate])->sum('total_amount'),
            'estimated_cost' => 0, // Would need cost price data
            'estimated_profit' => 0, // Would need cost price data
            'profit_margin' => 0, // Would need cost price data
        ];
    }
}