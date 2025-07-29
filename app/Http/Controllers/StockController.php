<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Services\StockService;
use Illuminate\Http\Request;

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
}
