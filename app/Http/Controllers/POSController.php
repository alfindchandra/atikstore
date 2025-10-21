<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\CashFlow;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class POSController extends Controller
{
    public function index()
    {
        $products = Product::with([
            'category', 
            'productUnits.unit',
            'productUnits.tieredPrices' => function($query) {
                $query->orderBy('min_quantity', 'asc');
            },
            'stocks.unit'
        ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pos.index', compact('products'));
    }

    public function searchProduct(Request $request)
    {
        try {
            $query = trim($request->get('query'));
            
            if (empty($query) || strlen($query) < 1) {
                return response()->json([]);
            }

            $products = Product::with([
                'category', 
                'productUnits.unit',
                'productUnits.tieredPrices' => function($query) {
                    $query->orderBy('min_quantity', 'asc');
                },
                'stocks.unit'
            ])
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
                            'min_purchase' => (int) ($productUnit->min_purchase ?: 1),
                            'max_purchase' => $productUnit->max_purchase ? (int) $productUnit->max_purchase : null,
                            'enable_tiered_pricing' => (bool) $productUnit->enable_tiered_pricing,
                            'tiered_prices' => $productUnit->tieredPrices->map(function ($tier) {
                                return [
                                    'min_quantity' => (int) $tier->min_quantity,
                                    'price' => (float) $tier->price,
                                    'description' => $tier->description,
                                ];
                            })->toArray(),
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
            Log::error('Search product error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    public function getProductByBarcode(Request $request)
    {
        try {
            $barcode = trim($request->get('barcode'));
            
            if (empty($barcode)) {
                return response()->json(['success' => false, 'message' => 'Barcode tidak boleh kosong']);
            }

            $product = Product::with([
                'category', 
                'productUnits.unit',
                'productUnits.tieredPrices' => function($query) {
                    $query->orderBy('min_quantity', 'asc');
                },
                'stocks.unit'
            ])
                ->where('is_active', true)
                ->where('barcode', $barcode)
                ->first();

            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan']);
            }

            $result = [
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
                        'min_purchase' => (int) ($productUnit->min_purchase ?: 1),
                        'max_purchase' => $productUnit->max_purchase ? (int) $productUnit->max_purchase : null,
                        'enable_tiered_pricing' => (bool) $productUnit->enable_tiered_pricing,
                        'tiered_prices' => $productUnit->tieredPrices->map(function ($tier) {
                            return [
                                'min_quantity' => (int) $tier->min_quantity,
                                'price' => (float) $tier->price,
                                'description' => $tier->description,
                            ];
                        })->toArray(),
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

            return response()->json(['success' => true, 'product' => $result]);

        } catch (\Exception $e) {
            Log::error('Get product by barcode error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
        }
    }

    public function calculateTieredPrice(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'unit_id' => 'required|integer|exists:units,id',
                'quantity' => 'required|numeric|min:0.01',
            ]);

            $productUnit = ProductUnit::with(['tieredPrices' => function($q) {
                $q->orderBy('min_quantity', 'desc');
            }])
            ->where('product_id', $validatedData['product_id'])
            ->where('unit_id', $validatedData['unit_id'])
            ->first();

            if (!$productUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Satuan produk tidak ditemukan'
                ]);
            }

            $quantity = $validatedData['quantity'];
            
            // Check min/max purchase limits
            $minPurchase = $productUnit->min_purchase ?: 1;
            $maxPurchase = $productUnit->max_purchase;

            if ($quantity < $minPurchase) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimal pembelian {$minPurchase} unit"
                ]);
            }

            if ($maxPurchase && $quantity > $maxPurchase) {
                return response()->json([
                    'success' => false,
                    'message' => "Maksimal pembelian {$maxPurchase} unit"
                ]);
            }

            // Calculate tiered price if enabled
            $finalPrice = $productUnit->price;
            $appliedTier = null;

            if ($productUnit->enable_tiered_pricing && $productUnit->tieredPrices->count() > 0) {
                foreach ($productUnit->tieredPrices as $tier) {
                    if ($quantity >= $tier->min_quantity) {
                        $finalPrice = $tier->price;
                        $appliedTier = [
                            'min_quantity' => $tier->min_quantity,
                            'price' => $tier->price,
                            'description' => $tier->description,
                        ];
                        break;
                    }
                }
            }

            $discountAmount = ($productUnit->price - $finalPrice) * $quantity;
            $discountPercentage = $productUnit->price > 0 ? round(($discountAmount / ($productUnit->price * $quantity)) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'base_price' => (float) $productUnit->price,
                    'final_price' => (float) $finalPrice,
                    'quantity' => (float) $quantity,
                    'subtotal' => (float) ($finalPrice * $quantity),
                    'applied_tier' => $appliedTier,
                    'min_purchase' => $minPurchase,
                    'max_purchase' => $maxPurchase,
                    'discount_amount' => (float) $discountAmount,
                    'discount_percentage' => $discountPercentage,
                    'total_discount' => (float) $discountAmount,
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Calculate tiered price error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function processTransaction(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate request data
            $validatedData = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.unit_id' => 'required|integer|exists:units,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'required|numeric|min:0',
                'additional_charges' => 'nullable|array',
                'additional_charges.*.description' => 'required|string',
                'additional_charges.*.amount' => 'required|numeric',
                'paid_amount' => 'required|numeric|min:0',
            ]);

            // Validate and process each item
            $subtotal = 0;
            $processedItems = [];

            foreach ($validatedData['items'] as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception('Produk dengan ID ' . $item['product_id'] . ' tidak ditemukan.');
                }

                $productUnit = ProductUnit::with(['tieredPrices' => function($q) {
                    $q->orderBy('min_quantity', 'desc');
                }])
                ->where('product_id', $item['product_id'])
                ->where('unit_id', $item['unit_id'])
                ->first();

                if (!$productUnit) {
                    throw new \Exception("Satuan tidak ditemukan untuk produk '{$product->name}'.");
                }

                // Check stock availability
                $stock = Stock::where('product_id', $item['product_id'])
                    ->where('unit_id', $item['unit_id'])
                    ->first();

                if (!$stock) {
                    throw new \Exception("Stok untuk produk '{$product->name}' tidak ditemukan.");
                }

                if ($stock->quantity < $item['quantity']) {
                    throw new \Exception("Stok untuk produk '{$product->name}' tidak mencukupi. Tersedia: {$stock->quantity}, Diminta: {$item['quantity']}");
                }

                // Validate purchase limits
                $minPurchase = $productUnit->min_purchase ?: 1;
                $maxPurchase = $productUnit->max_purchase;

                if ($item['quantity'] < $minPurchase) {
                    throw new \Exception("Produk '{$product->name}': Minimal pembelian {$minPurchase} unit.");
                }

                if ($maxPurchase && $item['quantity'] > $maxPurchase) {
                    throw new \Exception("Produk '{$product->name}': Maksimal pembelian {$maxPurchase} unit.");
                }

                // Verify tiered pricing calculation
                $expectedPrice = $productUnit->price;
                if ($productUnit->enable_tiered_pricing && $productUnit->tieredPrices->count() > 0) {
                    foreach ($productUnit->tieredPrices as $tier) {
                        if ($item['quantity'] >= $tier->min_quantity) {
                            $expectedPrice = $tier->price;
                            break;
                        }
                    }
                }

                // Allow small price differences due to floating point precision
                if (abs($item['unit_price'] - $expectedPrice) > 0.01) {
                    throw new \Exception("Harga untuk produk '{$product->name}' tidak sesuai. Expected: {$expectedPrice}, Received: {$item['unit_price']}");
                }

                $processedItems[] = [
                    'product' => $product,
                    'product_unit' => $productUnit,
                    'stock' => $stock,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price']
                ];

                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            
            // Calculate additional charges
            $additionalTotal = 0;
            if (isset($validatedData['additional_charges'])) {
                foreach ($validatedData['additional_charges'] as $charge) {
                    $additionalTotal += $charge['amount'];
                }
            }

            $totalAmount = $subtotal + $additionalTotal;
            $changeAmount = $validatedData['paid_amount'] - $totalAmount;

            // Check payment sufficiency
            if ($validatedData['paid_amount'] < $totalAmount) {
                throw new \Exception('Jumlah pembayaran tidak mencukupi. Total: ' . number_format($totalAmount, 0, ',', '.') . ', Dibayar: ' . number_format($validatedData['paid_amount'], 0, ',', '.'));
            }

            // Create main transaction
            $transaction = Transaction::create([
                'transaction_date' => now(),
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'paid_amount' => $validatedData['paid_amount'],
                'change_amount' => $changeAmount,
                'tax_amount' => $additionalTotal,
                'payment_method' => 'cash',
                'status' => 'completed',
                'notes' => $this->generateNotes($validatedData['additional_charges'] ?? []),
            ]);

            // Process each validated item
            foreach ($processedItems as $processedItem) {
                // Update stock
                $processedItem['stock']->decrement('quantity', $processedItem['quantity']);
                
                // Create transaction detail
                $transaction->details()->create([
                    'product_id' => $processedItem['product']->id,
                    'unit_id' => $processedItem['product_unit']->unit_id,
                    'quantity' => $processedItem['quantity'],
                    'unit_price' => $processedItem['unit_price'],
                    'subtotal' => $processedItem['subtotal'],
                ]);
                
                // Record stock movement
                StockMovement::create([
                    'product_id' => $processedItem['product']->id,
                    'unit_id' => $processedItem['product_unit']->unit_id,
                    'movement_type' => 'out',
                    'quantity' => $processedItem['quantity'],
                    'reference_type' => 'transaction',
                    'reference_id' => $transaction->id,
                    'notes' => 'Penjualan - ' . $transaction->transaction_number,
                    'movement_date' => now(),
                ]);
            }
            
            // Record cash flow
            CashFlow::create([
                'type' => 'income',
                'category' => 'Penjualan',
                'amount' => $totalAmount,
                'description' => 'Penjualan - ' . $transaction->transaction_number,
                'reference_type' => 'transaction',
                'reference_id' => $transaction->id,
                'transaction_date' => now(),
            ]);

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
            Log::error('Process transaction error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function generateNotes($additionalCharges)
    {
        if (empty($additionalCharges)) {
            return null;
        }

        $notes = "Biaya tambahan: ";
        $charges = [];
        
        foreach ($additionalCharges as $charge) {
            $amount = number_format(abs($charge['amount']), 0, ',', '.');
            $prefix = $charge['amount'] < 0 ? 'Diskon' : $charge['description'];
            $charges[] = "{$prefix} (Rp {$amount})";
        }

        return $notes . implode(', ', $charges);
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load(['details.product', 'details.unit']);
        return view('pos.print-receipt', compact('transaction'));
    }

    public function printReceipt(Transaction $transaction)
    {
        $transaction->load(['details.product', 'details.unit']);
        return view('pos.print-receipt', compact('transaction'));
    }
}