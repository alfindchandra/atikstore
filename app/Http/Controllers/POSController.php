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

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        $products = Product::with(['category', 'productUnits.unit', 'stocks'])
            ->where('is_active', true)
            ->get();

        $recentTransactions = $this->transactionService->getTodayTransactions()->take(10);

        return view('pos.index', compact('products', 'recentTransactions'));
    }

    public function searchProduct(Request $request)
    {
        $query = $request->get('query');
        
        $products = Product::with(['category', 'productUnits.unit', 'stocks'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json($products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'category' => $product->category->name,
                'units' => $product->productUnits->map(function ($productUnit) {
                    return [
                        'unit_id' => $productUnit->unit_id,
                        'unit_name' => $productUnit->unit->name,
                        'unit_symbol' => $productUnit->unit->symbol,
                        'price' => $productUnit->price,
                        'is_base_unit' => $productUnit->is_base_unit,
                    ];
                }),
                'stock_info' => $product->stocks->map(function ($stock) {
                    return [
                        'unit_id' => $stock->unit_id,
                        'unit_symbol' => $stock->unit->symbol,
                        'quantity' => $stock->quantity,
                    ];
                }),
            ];
        }));
    }

    public function processTransaction(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        try {
            $transaction = $this->transactionService->processTransaction(
                $request->items,
                $request->paid_amount
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses',
                'transaction' => $transaction,
                'receipt_url' => route('pos.receipt', $transaction->id)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

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
