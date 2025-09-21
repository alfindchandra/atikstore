<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // Import yang benar untuk Controller
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class APIPOSController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function searchProduct(Request $request)
    {
        try {
            $query = $request->get('query');
            
            // Validasi input query
            if (empty($query) || strlen($query) < 1) {
                return response()->json([]);
            }

            $products = Product::with(['category', 'productUnits.unit', 'stocks.unit'])
                ->where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('barcode', 'like', "%{$query}%");
                })
                ->limit(10)
                ->get();

            $result = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'sku' => $product->sku ?? null,
                    'category' => $product->category ? $product->category->name : 'Tidak ada kategori',
                    'units' => $product->productUnits->map(function ($productUnit) {
                        return [
                            'unit_id' => $productUnit->unit_id,
                            'unit_name' => $productUnit->unit->name ?? 'Unknown',
                            'unit_symbol' => $productUnit->unit->symbol ?? 'pcs',
                            'price' => (float) $productUnit->price,
                            'is_base_unit' => (bool) $productUnit->is_base_unit,
                        ];
                    })->toArray(),
                    'stock_info' => $product->stocks->map(function ($stock) {
                        return [
                            'unit_id' => $stock->unit_id,
                            'unit_symbol' => $stock->unit ? $stock->unit->symbol : 'pcs',
                            'quantity' => (float) $stock->quantity,
                        ];
                    })->toArray(),
                ];
            });

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error in searchProduct: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mencari produk',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function processTransaction(Request $request)
    {
        try {
            // 1. Validasi input
            $validatedData = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.unit_id' => 'required|integer|exists:units,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'required|numeric|min:0',
                'paid_amount' => 'required|numeric|min:0',
                'tax_amount' => 'required|numeric',
            ]);

            $items = collect($validatedData['items']);
            $paidAmount = $validatedData['paid_amount'];
            $taxAmount = $validatedData['tax_amount'];

            $subtotal = 0;
            $updatedItems = [];

            // 2. Validasi stok per item
            foreach ($items as $item) {
                $product = Product::with(['stocks' => function ($query) use ($item) {
                    $query->where('unit_id', $item['unit_id']);
                }])->find($item['product_id']);

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => "Produk dengan ID {$item['product_id']} tidak ditemukan."
                    ], 400);
                }

                $stock = $product->stocks->first();
                if (!$stock || $stock->quantity < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok untuk '{$product->name}' tidak mencukupi. 
                                      Tersedia: " . ($stock->quantity ?? 0) . 
                                      ", diminta: {$item['quantity']}"
                    ], 400);
                }

                $itemPrice = $item['unit_price'];
                $subtotal += ($itemPrice * $item['quantity']);

                $updatedItems[] = [
                    'product_id' => $product->id,
                    'unit_id'    => $item['unit_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $itemPrice, // konsisten dengan TransactionService
                ];
            }

            $totalAmount = $subtotal + $taxAmount;

            // 3. Validasi pembayaran
            if ($paidAmount < $totalAmount) {
                return response()->json([
                    'success' => false,
                    'message' => "Jumlah pembayaran kurang dari total transaksi. 
                                  Total: " . number_format($totalAmount, 2, ',', '.')
                ], 400);
            }

            // 4. Simpan transaksi lewat Service
            $transaction = $this->transactionService->processTransaction(
                $updatedItems,
                $paidAmount,
                $subtotal,
                $taxAmount,
                $request->notes ?? null
            );

            // 5. Response sukses
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses',
                'transaction' => [
                    'id'                 => $transaction->id,
                    'subtotal'           => $transaction->subtotal,
                    'transaction_number' => $transaction->transaction_number,
                    'total_amount'       => $transaction->total_amount,
                    'paid_amount'        => $transaction->paid_amount,
                    'tax_amount'         => $transaction->tax_amount,
                    'change_amount'      => $transaction->change_amount,
                    'created_at'         => $transaction->created_at,
                ],
                'receipt_url' => route('pos.print-receipt', $transaction->id)
            ]);

        } catch (\Throwable $e) {
            // log biar tahu kenapa rollback
            Log::error('ProcessTransaction Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProductByBarcode(Request $request)
    {
        try {
            $barcode = $request->get('barcode');
            
            if (empty($barcode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barcode tidak boleh kosong'
                ], 400);
            }

            $product = Product::with(['category', 'productUnits.unit', 'stocks.unit'])
                ->where('barcode', $barcode)
                ->where('is_active', true)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'category' => $product->category ? $product->category->name : 'Tidak ada kategori',
                    'units' => $product->productUnits->map(function ($productUnit) {
                        return [
                            'unit_id' => $productUnit->unit_id,
                            'unit_name' => $productUnit->unit->name ?? 'Unknown',
                            'unit_symbol' => $productUnit->unit->symbol ?? 'pcs',
                            'price' => (float) $productUnit->price,
                            'is_base_unit' => (bool) $productUnit->is_base_unit,
                        ];
                    })->toArray(),
                    'stock_info' => $product->stocks->map(function ($stock) {
                        return [
                            'unit_id' => $stock->unit_id,
                            'unit_symbol' => $stock->unit ? $stock->unit->symbol : 'pcs',
                            'quantity' => (float) $stock->quantity,
                        ];
                    })->toArray(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getProductByBarcode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari produk'
            ], 500);
        }
    }
}