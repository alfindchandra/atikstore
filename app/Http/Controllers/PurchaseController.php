<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseTransaction;
use App\Models\PurchaseTransactionDetail;
use App\Models\CashFlow;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = PurchaseTransaction::with(['supplier', 'details'])
            ->orderBy('purchase_date', 'desc')
            ->paginate(20);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $products = Product::with(['productUnits.unit'])->where('is_active', true)->orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Calculate totals
                $subtotal = 0;
                foreach ($request->items as $item) {
                    $subtotal += $item['quantity'] * $item['unit_cost'];
                }

                $taxAmount = $request->tax_amount ?? 0;
                $discountAmount = $request->discount_amount ?? 0;
                $totalAmount = $subtotal + $taxAmount - $discountAmount;

                // Handle receipt image upload
                $receiptImagePath = null;
                if ($request->hasFile('receipt_image')) {
                    $receiptImagePath = $request->file('receipt_image')->store('receipts', 'public');
                }

                // Create purchase transaction
                $purchase = PurchaseTransaction::create([
                    'supplier_id' => $request->supplier_id,
                    'purchase_date' => $request->purchase_date,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'receipt_image' => $receiptImagePath,
                    'notes' => $request->notes,
                    'status' => 'completed',
                ]);

                // Create purchase details and update stock
                foreach ($request->items as $item) {
                    // Create detail
                    $detail = PurchaseTransactionDetail::create([
                        'purchase_transaction_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'unit_id' => $item['unit_id'],
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['unit_cost'],
                        'subtotal' => $item['quantity'] * $item['unit_cost'],
                    ]);

                    // Update stock
                    $stock = Stock::where('product_id', $item['product_id'])
                        ->where('unit_id', $item['unit_id'])
                        ->first();

                    if ($stock) {
                        $stock->increment('quantity', $item['quantity']);
                    } else {
                        Stock::create([
                            'product_id' => $item['product_id'],
                            'unit_id' => $item['unit_id'],
                            'quantity' => $item['quantity'],
                        ]);
                    }

                    // Record stock movement
                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'unit_id' => $item['unit_id'],
                        'movement_type' => 'in',
                        'quantity' => $item['quantity'],
                        'reference_type' => 'purchase',
                        'reference_id' => $purchase->id,
                        'notes' => 'Pembelian dari ' . $purchase->supplier->name,
                        'movement_date' => $request->purchase_date,
                    ]);
                }

                // Record cash flow
                CashFlow::create([
                    'type' => 'expense',
                    'category' => 'Pembelian Barang',
                    'amount' => $totalAmount,
                    'description' => 'Pembelian dari ' . $purchase->supplier->name . ' - ' . $purchase->purchase_number,
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'transaction_date' => $request->purchase_date,
                ]);
            });

            return redirect()->route('purchases.index')
                ->with('success', 'Transaksi pembelian berhasil disimpan');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(PurchaseTransaction $purchase)
    {
        $purchase->load(['supplier', 'details.product', 'details.unit']);
        return view('purchases.show', compact('purchase'));
    }

    public function edit(PurchaseTransaction $purchase)
    {
        $purchase->load(['details.product', 'details.unit']);
        $suppliers = Supplier::active()->orderBy('name')->get();
        $products = Product::with(['productUnits.unit'])->where('is_active', true)->orderBy('name')->get();

        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseTransaction $purchase)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request, $purchase) {
                // Revert previous stock changes
                foreach ($purchase->details as $detail) {
                    $stock = Stock::where('product_id', $detail->product_id)
                        ->where('unit_id', $detail->unit_id)
                        ->first();

                    if ($stock) {
                        $stock->decrement('quantity', $detail->quantity);
                    }
                }

                // Delete old details
                $purchase->details()->delete();

                // Calculate new totals
                $subtotal = 0;
                foreach ($request->items as $item) {
                    $subtotal += $item['quantity'] * $item['unit_cost'];
                }

                $taxAmount = $request->tax_amount ?? 0;
                $discountAmount = $request->discount_amount ?? 0;
                $totalAmount = $subtotal + $taxAmount - $discountAmount;

                // Handle receipt image upload
                $receiptImagePath = $purchase->receipt_image;
                if ($request->hasFile('receipt_image')) {
                    // Delete old image
                    if ($purchase->receipt_image) {
                        Storage::disk('public')->delete($purchase->receipt_image);
                    }
                    $receiptImagePath = $request->file('receipt_image')->store('receipts', 'public');
                }

                // Update purchase transaction
                $purchase->update([
                    'supplier_id' => $request->supplier_id,
                    'purchase_date' => $request->purchase_date,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'receipt_image' => $receiptImagePath,
                    'notes' => $request->notes,
                ]);

                // Create new details and update stock
                foreach ($request->items as $item) {
                    PurchaseTransactionDetail::create([
                        'purchase_transaction_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'unit_id' => $item['unit_id'],
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['unit_cost'],
                        'subtotal' => $item['quantity'] * $item['unit_cost'],
                    ]);

                    // Update stock
                    $stock = Stock::where('product_id', $item['product_id'])
                        ->where('unit_id', $item['unit_id'])
                        ->first();

                    if ($stock) {
                        $stock->increment('quantity', $item['quantity']);
                    } else {
                        Stock::create([
                            'product_id' => $item['product_id'],
                            'unit_id' => $item['unit_id'],
                            'quantity' => $item['quantity'],
                        ]);
                    }

                    // Record stock movement
                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'unit_id' => $item['unit_id'],
                        'movement_type' => 'in',
                        'quantity' => $item['quantity'],
                        'reference_type' => 'purchase',
                        'reference_id' => $purchase->id,
                        'notes' => 'Update pembelian dari ' . $purchase->supplier->name,
                        'movement_date' => $request->purchase_date,
                    ]);
                }

                // Update cash flow
                $cashFlow = CashFlow::where('reference_type', 'purchase')
                    ->where('reference_id', $purchase->id)
                    ->first();

                if ($cashFlow) {
                    $cashFlow->update([
                        'amount' => $totalAmount,
                        'description' => 'Pembelian dari ' . $purchase->supplier->name . ' - ' . $purchase->purchase_number . ' (Updated)',
                        'transaction_date' => $request->purchase_date,
                    ]);
                }
            });

            return redirect()->route('purchases.index')
                ->with('success', 'Transaksi pembelian berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui transaksi: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(PurchaseTransaction $purchase)
    {
        try {
            DB::transaction(function () use ($purchase) {
                // Revert stock changes
                foreach ($purchase->details as $detail) {
                    $stock = Stock::where('product_id', $detail->product_id)
                        ->where('unit_id', $detail->unit_id)
                        ->first();

                    if ($stock && $stock->quantity >= $detail->quantity) {
                        $stock->decrement('quantity', $detail->quantity);
                    }
                }

                // Delete receipt image
                if ($purchase->receipt_image) {
                    Storage::disk('public')->delete($purchase->receipt_image);
                }

                // Delete cash flow
                CashFlow::where('reference_type', 'purchase')
                    ->where('reference_id', $purchase->id)
                    ->delete();

                // Delete stock movements
                StockMovement::where('reference_type', 'purchase')
                    ->where('reference_id', $purchase->id)
                    ->delete();

                // Delete purchase
                $purchase->delete();
            });

            return redirect()->route('purchases.index')
                ->with('success', 'Transaksi pembelian berhasil dihapus');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus transaksi: ' . $e->getMessage()]);
        }
    }
    public function receipt(PurchaseTransaction $purchase)
{
    $purchase->load(['supplier', 'details.product', 'details.unit']);
    return view('purchases.receipt', compact('purchase'));
}

public function downloadReceipt(PurchaseTransaction $purchase)
{
    $purchase->load(['supplier', 'details.product', 'details.unit']);
    
    // Generate PDF using DomPDF (optional)
    // $pdf = PDF::loadView('purchases.receipt-pdf', compact('purchase'));
    // return $pdf->download('purchase-receipt-' . $purchase->purchase_number . '.pdf');
    
    // For now, just show the receipt view
    return view('purchases.receipt', compact('purchase'));
}

// Juga tambahkan method untuk API jika diperlukan
public function getReceiptData(PurchaseTransaction $purchase)
{
    $purchase->load(['supplier', 'details.product', 'details.unit']);
    
    return response()->json([
        'success' => true,
        'data' => [
            'purchase' => $purchase,
            'supplier' => $purchase->supplier,
            'details' => $purchase->details,
            'receipt_url' => $purchase->receipt_image_url
        ]
    ]);
}
}