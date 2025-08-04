<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        $products = Product::with(['category', 'productUnits.unit', 'stocks.unit'])
            ->orderBy('name')
            ->get();

        $lowStockProducts = collect($products)->filter(function ($product) {
    return $product->isLowStock();
});

        return view('stock.index', compact('products', 'lowStockProducts'));
    }

    public function adjustment()
    {
        $products = Product::with(['productUnits.unit'])->where('is_active', true)->get();
        return view('stock.adjustment', compact('products'));
    }

    public function processAdjustment(Request $request)
    {
        $request->validate([
            'adjustments' => 'required|array|min:1',
            'adjustments.*.product_id' => 'required|exists:products,id',
            'adjustments.*.unit_id' => 'required|exists:units,id',
            'adjustments.*.quantity' => 'required|numeric',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            foreach ($request->adjustments as $adjustment) {
                if ($adjustment['quantity'] != 0) {
                    $this->stockService->updateStock(
                        $adjustment['product_id'],
                        $adjustment['unit_id'],
                        abs($adjustment['quantity']),
                        $adjustment['quantity'] > 0 ? 'in' : 'out',
                        'adjustment',
                        null,
                        $request->notes ?? 'Penyesuaian stok manual'
                    );
                }
            }

            return redirect()->route('stock.index')
                ->with('success', 'Penyesuaian stok berhasil diproses');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memproses penyesuaian: ' . $e->getMessage()]);
        }
    }

    public function movement()
    {
        $movements = StockMovement::with(['product', 'unit'])
            ->orderBy('movement_date', 'desc')
            ->paginate(50);

        return view('stock.movement', compact('movements'));
    }

    public function productStock(Product $product)
    {
        $product->load(['category', 'productUnits.unit', 'stocks.unit', 'stockMovements.unit']);
        
        return view('stock.product', compact('product'));
    }
    public function edit(Product $product)
{
    $product->load(['category', 'productUnits.unit', 'stocks.unit']);
    return view('stock.edit', compact('product'));
}

public function update(Request $request, Product $product)
{
    $request->validate([
        'stocks' => 'required|array|min:1',
        'stocks.*.unit_id' => 'required|exists:units,id',
        'stocks.*.quantity' => 'required|numeric|min:0',
        'notes' => 'nullable|string|max:500',
    ]);

    try {
        DB::transaction(function () use ($request, $product) {
            foreach ($request->stocks as $stockData) {
                $stock = Stock::where('product_id', $product->id)
                    ->where('unit_id', $stockData['unit_id'])
                    ->first();

                if ($stock) {
                    $oldQuantity = $stock->quantity;
                    $newQuantity = $stockData['quantity'];
                    $difference = $newQuantity - $oldQuantity;

                    // Update stock
                    $stock->update(['quantity' => $newQuantity]);

                    // Record stock movement if there's a change
                    if ($difference != 0) {
                        StockMovement::create([
                            'product_id' => $product->id,
                            'unit_id' => $stockData['unit_id'],
                            'movement_type' => $difference > 0 ? 'in' : 'out',
                            'quantity' => abs($difference),
                            'reference_type' => 'adjustment',
                            'reference_id' => null,
                            'notes' => $request->notes ?? 'Edit stok manual',
                            'movement_date' => now(),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('stock.index')
            ->with('success', 'Stok berhasil diperbarui');

    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Gagal memperbarui stok: ' . $e->getMessage()]);
    }
}
}
