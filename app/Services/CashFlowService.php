<?php
namespace App\Services;

use App\Models\CashFlow;
use Illuminate\Support\Facades\DB;

class CashFlowService
{
    public function addIncome(float $amount, string $category, string $description): CashFlow
    {
        return CashFlow::create([
            'type' => 'income',
            'category' => $category,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => now(),
        ]);
    }

    public function addExpense(float $amount, string $category, string $description): CashFlow
    {
        return CashFlow::create([
            'type' => 'expense',
            'category' => $category,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => now(),
        ]);
    }

    public function getDailyCashFlow(string $date = null): array
    {
        $targetDate = $date ? \Carbon\Carbon::parse($date) : now();
        
        $income = CashFlow::income()
            ->whereDate('transaction_date', $targetDate)
            ->sum('amount');

        $expense = CashFlow::expense()
            ->whereDate('transaction_date', $targetDate)
            ->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'net' => $income - $expense,
            'date' => $targetDate->format('Y-m-d'),
        ];
    }

    public function getMonthlyCashFlow(int $month = null, int $year = null): array
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $income = CashFlow::income()
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $expense = CashFlow::expense()
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'net' => $income - $expense,
            'month' => $month,
            'year' => $year,
        ];
    }

    public function getRecentTransactions(int $limit = 10): array
    {
        return CashFlow::with(['reference'])
            ->orderBy('transaction_date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($cashFlow) {
                return [
                    'id' => $cashFlow->id,
                    'type' => $cashFlow->type,
                    'category' => $cashFlow->category,
                    'amount' => $cashFlow->amount,
                    'description' => $cashFlow->description,
                    'date' => $cashFlow->transaction_date->format('d/m/Y H:i'),
                ];
            })
            ->toArray();
    }
}
