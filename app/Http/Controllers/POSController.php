<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\CashFlow;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class POSController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'productUnits.unit', 'stocks.unit'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pos.index', compact('products'));
    }

    public function searchProduct(Request $request)
    {
        $query = $request->get('query');
        
        if (empty($query) || strlen($query) < 1) {
            return response()->json([]);
        }

        $products = Product::with(['category', 'productUnits.unit', 'stocks.unit'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get();

        $result = $products->map(function ($product) {
            return [
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
            ];
        });

        return response()->json($result);
    }

    public function processTransaction(Request $request)
    {DB::beginTransaction();

        try {
            // 1. Validasi Input Klien
            $validatedData = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.unit_id' => 'required|integer|exists:units,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'required|numeric|min:0',
                'additional_charges' => 'nullable|array',
                'additional_charges.*.description' => 'required|string',
                'additional_charges.*.amount' => 'required|numeric', // Bisa negatif untuk diskon
                'paid_amount' => 'required|numeric|min:0',
            ]);

            // 2. Lakukan Validasi Stok & Hitung Total di Server
            $subtotal = 0;
            $itemsToProcess = collect($validatedData['items']);

            foreach ($itemsToProcess as $item) {
                // Eager load relasi 'stocks' untuk efisiensi query
                $product = Product::with(['stocks' => fn ($query) => $query->where('unit_id', $item['unit_id'])])
                                  ->find($item['product_id']);

                if (!$product) {
                    throw new \Exception('Produk dengan ID ' . $item['product_id'] . ' tidak ditemukan.');
                }

                $stock = $product->stocks->first();
                if (!$stock || $stock->quantity < $item['quantity']) {
                    throw new \Exception("Stok untuk produk '{$product->name}' tidak mencukupi. Tersedia: {$stock->quantity}.");
                }

                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            
            // Hitung total biaya tambahan (termasuk diskon)
            $additionalTotal = 0;
            if (isset($validatedData['additional_charges'])) {
                foreach ($validatedData['additional_charges'] as $charge) {
                    $additionalTotal += $charge['amount'];
                }
            }

            $totalAmount = $subtotal + $additionalTotal;
            $changeAmount = $validatedData['paid_amount'] - $totalAmount;

            // 3. Validasi Uang Pembayaran
            if ($validatedData['paid_amount'] < $totalAmount) {
                throw new \Exception('Jumlah pembayaran kurang dari total transaksi.');
            }

            // 4. Proses Transaksi di dalam satu blok atomik
            // Kode ini akan dieksekusi hanya jika validasi di atas berhasil
            
            // Buat transaksi utama
            $transaction = Transaction::create([
                'transaction_date' => now(),
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'paid_amount' => $validatedData['paid_amount'],
                'change_amount' => $changeAmount,
                'tax_amount' => $additionalTotal, // Simpan total biaya tambahan di sini
                'payment_method' => 'cash',
                'status' => 'completed',
                'notes' => $this->generateNotes($validatedData['additional_charges'] ?? []),
            ]);

            // Buat detail transaksi dan update stok untuk setiap item
            foreach ($itemsToProcess as $item) {
                // Kurangi stok di database
                $stock = Stock::where('product_id', $item['product_id'])
                               ->where('unit_id', $item['unit_id'])
                               ->first();

                $stock->decrement('quantity', $item['quantity']);
                
                // Buat entri detail transaksi
                $transaction->details()->create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);
                
                // Catat pergerakan stok
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'movement_type' => 'out',
                    'quantity' => $item['quantity'],
                    'reference_type' => 'transaction',
                    'reference_id' => $transaction->id,
                    'notes' => 'Penjualan - ' . $transaction->transaction_number,
                    'movement_date' => now(),
                ]);
            }
            
            // Catat pergerakan cash flow
            CashFlow::create([
                'type' => 'income',
                'category' => 'Penjualan',
                'amount' => $totalAmount,
                'description' => 'Penjualan - ' . $transaction->transaction_number,
                'reference_type' => 'transaction',
                'reference_id' => $transaction->id,
                'transaction_date' => now(),
            ]);

            // Jika semua berhasil, commit transaksi database
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses',
                'transaction' => [
                    'id' => $transaction->id,
                    'transaction_number' => $transaction->transaction_number,
                    'subtotal' => $transaction->subtotal,
                    'total_amount' => $transaction->total_amount,
                    'paid_amount' => $transaction->paid_amount,
                    'change_amount' => $transaction->change_amount,
                    'tax_amount' => $transaction->tax_amount,
                    'notes' => $transaction->notes,
                    'created_at' => $transaction->created_at,
                ],
                'receipt_url' => route('pos.receipt', $transaction->id)
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in processTransaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
    public function receipt(Transaction $transaction)
    {
        $transaction->load(['details.product', 'details.unit']);
        return view('pos.receipt', compact('transaction'));
    }

    public function printReceipt(Transaction $transaction)
    {
        $transaction->load(['details.product', 'details.unit']);
        return view('pos.print-receipt', compact('transaction'));
    }
}