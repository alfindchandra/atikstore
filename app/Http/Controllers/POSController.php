<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class POSController extends Controller
{
    protected TransactionService $transactionService;

    // Perhatikan: Konstruktor TransactionService tetap ada jika metode lain di controller ini membutuhkannya
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        // Data ini mungkin tidak sepenuhnya relevan lagi jika frontend sudah full API,
        // tapi tetap dipertahankan jika ada elemen tampilan yang memerlukannya.
        $products = Product::with(['category', 'productUnits.unit', 'stocks'])
            ->where('is_active', true)
            ->get();

        $recentTransactions = $this->transactionService->getTodayTransactions()->take(10);

        return view('pos.index', compact('products', 'recentTransactions'));
    }

    // Metode searchProduct dan processTransaction telah dipindahkan ke APIPOSController.php
    // public function searchProduct(Request $request) { ... }
    // public function processTransaction(Request $request) { ... }

    public function receipt($transactionId)
    {
        $transaction = Transaction::with(['details.product', 'details.unit'])
            ->findOrFail($transactionId);

        return view('pos.receipt', compact('transaction'));
    }

    public function printReceipt($transactionId)
    {
        $transaction = Transaction::with(['details.product', 'details.unit'])
            ->findOrFail($transactionId);

        $pdf = PDF::loadView('pos.receipt-pdf', compact('transaction'));
        $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm width thermal paper

        return $pdf->stream('receipt-' . $transaction->transaction_number . '.pdf');
    }
}