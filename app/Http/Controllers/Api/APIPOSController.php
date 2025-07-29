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
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                
                'paid_amount' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
            ]);

            // Validasi bahwa jumlah yang dibayar >= total
            if ($request->paid_amount < $request->total_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran kurang dari total'
                ], 400);
            }

            // Validasi stok untuk setiap item
            foreach ($request->items as $item) {
                $product = Product::with(['stocks' => function($query) use ($item) {
                    $query->where('unit_id', $item['unit_id']);
                }])->find($item['product_id']);

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Produk tidak ditemukan'
                    ], 400);
                }

                // Cek stok jika ada
                $stock = $product->stocks->first();
                if ($stock && $stock->quantity < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$product->name} tidak mencukupi. Tersedia: {$stock->quantity}"
                    ], 400);
                }
            }

            $transaction = $this->transactionService->processTransaction(
                $request->items,
                $request->paid_amount,
                $request->total_amount ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses',
                'transaction' => [
                    'id' => $transaction->id,
                    'subtotal' => $transaction->subtotal, 
                    'transaction_number' => $transaction->transaction_number,
                    'total_amount' => $transaction->total_amount,
                    'paid_amount' => $transaction->paid_amount,
                    'change_amount' => $transaction->change_amount,
                    'created_at' => $transaction->created_at,
                ],
                'receipt_url' => route('pos.receipt', $transaction->id)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error in processTransaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Method tambahan untuk mendapatkan detail produk berdasarkan barcode
     */
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