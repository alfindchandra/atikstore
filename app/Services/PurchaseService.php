<?php

namespace App\Services;

use App\Models\PurchaseTransaction;
use App\Models\PurchaseTransactionDetail;
use App\Models\CashFlow;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseService
{
    /**
     * Process purchase transaction
     */
    public function processPurchase(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Calculate totals
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_cost'];
            }

            $taxAmount = $data['tax_amount'] ?? 0;
            $discountAmount = $data['discount_amount'] ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            // Handle receipt image upload
            $receiptImagePath = null;
            if (isset($data['receipt_image'])) {
                $receiptImagePath = $data['receipt_image']->store('receipts', 'public');
            }

            // Create purchase transaction
            $purchase = PurchaseTransaction::create([
                'supplier_id' => $data['supplier_id'],
                'purchase_date' => $data['purchase_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'receipt_image' => $receiptImagePath,
                'notes' => $data['notes'] ?? null,
                'status' => 'completed',
            ]);

            // Process items
            foreach ($data['items'] as $item) {
                $this->processItem($purchase, $item);
            }

            // Record cash flow
            $this->recordCashFlow($purchase);

            return $purchase;
        });
    }

    /**
     * Update purchase transaction
     */
    public function updatePurchase(PurchaseTransaction $purchase, array $data)
    {
        return DB::transaction(function () use ($purchase, $data) {
            // Revert previous stock changes
            $this->revertStockChanges($purchase);

            // Delete old details
            $purchase->details()->delete();

            // Calculate new totals
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_cost'];
            }

            $taxAmount = $data['tax_amount'] ?? 0;
            $discountAmount = $data['discount_amount'] ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            // Handle receipt image upload
            $receiptImagePath = $purchase->receipt_image;
            if (isset($data['receipt_image'])) {
                // Delete old image
                if ($purchase->receipt_image) {
                    Storage::disk('public')->delete($purchase->receipt_image);
                }
                $receiptImagePath = $data['receipt_image']->store('receipts', 'public');
            }

            // Update purchase transaction
            $purchase->update([
                'supplier_id' => $data['supplier_id'],
                'purchase_date' => $data['purchase_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'receipt_image' => $receiptImagePath,
                'notes' => $data['notes'] ?? null,
            ]);

            // Process new items
            foreach ($data['items'] as $item) {
                $this->processItem($purchase, $item);
            }

            // Update cash flow
            $this->updateCashFlow($purchase);

            return $purchase;
        });
    }

    /**
     * Delete purchase transaction
     */
    public function deletePurchase(PurchaseTransaction $purchase)
    {
        return DB::transaction(function () use ($purchase) {
            // Revert stock changes
            $this->revertStockChanges($purchase);

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

            return true;
        });
    }

    /**
     * Process individual item
     */
    private function processItem(PurchaseTransaction $purchase, array $item)
    {
        // Create detail
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
            'notes' => 'Pembelian dari ' . $purchase->supplier->name,
            'movement_date' => $purchase->purchase_date,
        ]);
    }

    /**
     * Revert stock changes
     */
    private function revertStockChanges(PurchaseTransaction $purchase)
    {
        foreach ($purchase->details as $detail) {
            $stock = Stock::where('product_id', $detail->product_id)
                ->where('unit_id', $detail->unit_id)
                ->first();

            if ($stock && $stock->quantity >= $detail->quantity) {
                $stock->decrement('quantity', $detail->quantity);
            }
        }
    }

    /**
     * Record cash flow
     */
    private function recordCashFlow(PurchaseTransaction $purchase)
    {
        CashFlow::create([
            'type' => 'expense',
            'category' => 'Pembelian Barang',
            'amount' => $purchase->total_amount,
            'description' => 'Pembelian dari ' . $purchase->supplier->name . ' - ' . $purchase->purchase_number,
            'reference_type' => 'purchase',
            'reference_id' => $purchase->id,
            'transaction_date' => $purchase->purchase_date,
        ]);
    }

    /**
     * Update cash flow
     */
    private function updateCashFlow(PurchaseTransaction $purchase)
    {
        $cashFlow = CashFlow::where('reference_type', 'purchase')
            ->where('reference_id', $purchase->id)
            ->first();

        if ($cashFlow) {
            $cashFlow->update([
                'amount' => $purchase->total_amount,
                'description' => 'Pembelian dari ' . $purchase->supplier->name . ' - ' . $purchase->purchase_number . ' (Updated)',
                'transaction_date' => $purchase->purchase_date,
            ]);
        } else {
            $this->recordCashFlow($purchase);
        }
    }

    /**
     * Get purchase statistics
     */
    public function getPurchaseStats($dateFrom = null, $dateTo = null)
    {
        $query = PurchaseTransaction::query();

        if ($dateFrom && $dateTo) {
            $query->whereBetween('purchase_date', [$dateFrom, $dateTo]);
        }

        return [
            'total_purchases' => $query->count(),
            'total_amount' => $query->sum('total_amount'),
            'total_items' => $query->withCount('details')->get()->sum('details_count'),
            'active_suppliers' => $query->distinct('supplier_id')->count(),
        ];
    }

    /**
     * Get top suppliers by purchase amount
     */
    public function getTopSuppliers($limit = 5, $dateFrom = null, $dateTo = null)
    {
        $query = PurchaseTransaction::with('supplier')
            ->selectRaw('supplier_id, COUNT(*) as purchase_count, SUM(total_amount) as total_amount')
            ->groupBy('supplier_id')
            ->orderBy('total_amount', 'desc')
            ->limit($limit);

        if ($dateFrom && $dateTo) {
            $query->whereBetween('purchase_date', [$dateFrom, $dateTo]);
        }

        return $query->get();
    }

    /**
     * Get recent purchases
     */
    public function getRecentPurchases($limit = 10)
    {
        return PurchaseTransaction::with(['supplier', 'details'])
            ->orderBy('purchase_date', 'desc')
            ->limit($limit)
            ->get();
    }
}