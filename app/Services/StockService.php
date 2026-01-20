<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function updateStock(
        int $productId,
        int $unitId,
        float $quantity,
        string $movementType,
        string $referenceType = null,
        int $referenceId = null,
        string $notes = null
    ): bool {
        return DB::transaction(function () use (
            $productId,
            $unitId,
            $quantity,
            $movementType,
            $referenceType,
            $referenceId,
            $notes
        ) {
            // Cari atau buat stock record
            $stock = Stock::firstOrCreate(
                ['product_id' => $productId, 'unit_id' => $unitId],
                ['quantity' => 0]
            );

            // Update quantity berdasarkan movement type
            if ($movementType === 'in' || $movementType === 'adjustment') {
                $stock->quantity += $quantity;
            } elseif ($movementType === 'out') {
                if ($stock->quantity < $quantity) {
                    throw new \Exception('Stock tidak mencukupi');
                }
                $stock->quantity -= $quantity;
            }

            $stock->save();

            // Catat stock movement
            StockMovement::create([
                'product_id' => $productId,
                'unit_id' => $unitId,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'movement_date' => now(),
            ]);

            return true;
        });
    }

    public function getStockByProduct(Product $product): array
    {
        return $product->stocks()
            ->with(['unit'])
            ->get()
            ->map(function ($stock) {
                return [
                    'unit' => $stock->unit->name,
                    'symbol' => $stock->unit->symbol,
                    'quantity' => $stock->quantity,
                ];
            })
            ->toArray();
    }

    public function getLowStockProducts(): array
    {
        return Product::with(['category', 'productUnits.unit', 'stocks'])
            ->get()
            ->filter(function ($product) {
                return $product->isLowStock();
            })
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name,
                    'current_stock' => $product->getTotalStockInBaseUnit(),
                    'minimum_stock' => $product->stock_alert_minimum,
                    'base_unit' => $product->getBaseUnit()?->unit->symbol,
                ];
            })
            ->values()
            ->toArray();
    }
}
