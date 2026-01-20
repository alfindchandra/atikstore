<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\CashFlow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    /**
     * Process a complete transaction
     */
    public function processTransaction(
        array $items,
        float $paidAmount,
        float $subtotal,
        float $taxAmount = 0,
        ?string $notes = null
    ): Transaction {
        return DB::transaction(function () use ($items, $paidAmount, $subtotal, $taxAmount, $notes) {
            // 1. Create transaction record
            $totalAmount = $subtotal + $taxAmount;
            $changeAmount = $paidAmount - $totalAmount;

            $transaction = Transaction::create([
                'transaction_date' => now(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => 'cash',
                'note' => $notes,
                'status' => 'completed',
            ]);

            // 2. Create transaction details and update stock
            foreach ($items as $item) {
                // Create detail
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                // Update stock
                $this->updateStock(
                    $item['product_id'],
                    $item['unit_id'],
                    $item['quantity'],
                    'out',
                    'transaction',
                    $transaction->id
                );
            }

            // 3. Record cash flow
            CashFlow::create([
                'type' => 'income',
                'category' => 'Penjualan',
                'amount' => $totalAmount,
                'description' => 'Penjualan - ' . $transaction->transaction_number,
                'reference_type' => 'transaction',
                'reference_id' => $transaction->id,
                'transaction_date' => now(),
            ]);

            return $transaction->fresh(['details.product', 'details.unit']);
        });
    }

    /**
     * Update stock and create movement record
     */
    protected function updateStock(
        int $productId,
        int $unitId,
        float $quantity,
        string $type,
        string $referenceType,
        int $referenceId
    ): void {
        $stock = Stock::where('product_id', $productId)
            ->where('unit_id', $unitId)
            ->first();

        if (!$stock) {
            throw new \Exception("Stock record not found for product {$productId} and unit {$unitId}");
        }

        // Update stock quantity
        if ($type === 'out') {
            if ($stock->quantity < $quantity) {
                throw new \Exception("Insufficient stock for product ID {$productId}");
            }
            $stock->decrement('quantity', $quantity);
        } else {
            $stock->increment('quantity', $quantity);
        }

        // Record stock movement
        StockMovement::create([
            'product_id' => $productId,
            'unit_id' => $unitId,
            'movement_type' => $type,
            'quantity' => $quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => ucfirst($referenceType) . ' - ' . $referenceId,
            'movement_date' => now(),
        ]);
    }

    /**
     * Get today's transactions
     */
    public function getTodayTransactions()
    {
        return Transaction::with(['details.product', 'details.unit'])
            ->whereDate('transaction_date', today())
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /**
     * Get transaction by ID
     */
    public function getTransaction(int $id): ?Transaction
    {
        return Transaction::with(['details.product', 'details.unit'])
            ->find($id);
    }

    /**
     * Cancel a transaction
     */
    public function cancelTransaction(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $transaction = Transaction::findOrFail($id);

            if ($transaction->status === 'cancelled') {
                throw new \Exception('Transaction already cancelled');
            }

            // Revert stock changes
            foreach ($transaction->details as $detail) {
                $this->updateStock(
                    $detail->product_id,
                    $detail->unit_id,
                    $detail->quantity,
                    'in', // Reverse the out movement
                    'transaction_cancel',
                    $transaction->id
                );
            }

            // Update cash flow
            $cashFlow = CashFlow::where('reference_type', 'transaction')
                ->where('reference_id', $transaction->id)
                ->first();

            if ($cashFlow) {
                $cashFlow->update([
                    'description' => $cashFlow->description . ' (DIBATALKAN)',
                ]);
            }

            // Update transaction status
            $transaction->update(['status' => 'cancelled']);

            return true;
        });
    }
}