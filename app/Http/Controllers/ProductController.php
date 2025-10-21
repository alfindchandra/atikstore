<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\TieredPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'productUnits.unit', 'stocks'])
            ->orderBy('name')
            ->paginate(20);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.create', compact('categories', 'units'));
    }
public function store(Request $request)
    {
        // 1. Validasi permintaan
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => [
                'nullable',
                'string',
                Rule::unique('products', 'barcode')->whereNotNull('barcode')
            ],
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'stock_alert_minimum' => 'required|numeric|min:0',
            'units' => 'required|array|min:1',
            'units.*.unit_id' => 'required|exists:units,id|distinct',
            'units.*.price' => 'required|numeric|min:0',
            'units.*.conversion_rate' => 'required|numeric|min:0.0001',
            'units.*.is_base_unit' => 'nullable|boolean',
            'units.*.min_purchase' => 'nullable|numeric|min:1',
            'units.*.max_purchase' => 'nullable|numeric|gte:units.*.min_purchase',
            'units.*.enable_tiered_pricing' => 'nullable|boolean', // Menambahkan validasi ini
            'units.*.tiered_prices' => [
                'nullable',
                'array',
                'required_if:units.*.enable_tiered_pricing,true',
            ],
            'units.*.tiered_prices.*.min_quantity' => [
                'required',
                'numeric',
                'min:1',
                'distinct',
            ],
            'units.*.tiered_prices.*.price' => 'required|numeric|min:0',
            'units.*.tiered_prices.*.description' => 'nullable|string',
        ]);
    
        // 2. Validasi kustom untuk memastikan hanya ada satu base unit
        $baseUnits = collect($request->units)->where('is_base_unit', true);
        if ($baseUnits->count() !== 1) {
            return back()->withInput()->withErrors(['units' => 'Harus ada tepat satu satuan dasar.']);
        }
    
        try {
            // 3. Gunakan transaksi database untuk memastikan data konsisten
            DB::transaction(function () use ($request) {
                // 4. Buat produk baru
                $product = Product::create([
                    'name' => $request->name,
                    'barcode' => $request->barcode,
                    'category_id' => $request->category_id,
                    'description' => $request->description,
                    'stock_alert_minimum' => $request->stock_alert_minimum,
                ]);
    
                // 5. Perulangan untuk setiap unit produk
                foreach ($request->units as $unitData) {
                    $productUnit = ProductUnit::create([
                        'product_id' => $product->id,
                        'unit_id' => $unitData['unit_id'],
                        'price' => $unitData['price'],
                        'conversion_rate' => $unitData['conversion_rate'],
                        'is_base_unit' => $unitData['is_base_unit'] ?? false,
                        'min_purchase' => $unitData['min_purchase'] ?? 1,
                        'max_purchase' => $unitData['max_purchase'] ?? null,
                    ]);
    
                    // 6. Buat stock awal untuk unit dasar
                    if ($productUnit->is_base_unit) {
                        Stock::create([
                            'product_id' => $product->id,
                            'unit_id' => $productUnit->unit_id,
                            'quantity' => 10, // Atau sesuaikan dengan nilai default yang Anda inginkan
                        ]);
                    }
    
                    // 7. Perulangan untuk harga berjenjang (tiered prices) jika diaktifkan
                    if (($unitData['enable_tiered_pricing'] ?? false) && isset($unitData['tiered_prices']) && is_array($unitData['tiered_prices'])) {
                        foreach ($unitData['tiered_prices'] as $tieredPriceData) {
                            TieredPrice::create([
                                'product_unit_id' => $productUnit->id,
                                'min_quantity' => $tieredPriceData['min_quantity'],
                                'price' => $tieredPriceData['price'],
                                'description' => $tieredPriceData['description'] ?? null,
                            ]);
                        }
                    }
                }
            });
    
            // 8. Redirect dengan pesan sukses
            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            // 9. Tangani error dan kembalikan pesan
            return back()->withInput()->withErrors(['error' => 'Gagal menambahkan produk: ' . $e->getMessage()]);
        }
    }
    public function edit(Product $product)
    {
        $product->load(['category', 'productUnits.unit', 'stocks.unit']);
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'units'));
    }
        public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'stock_alert_minimum' => 'required|numeric|min:0',
            'units' => 'required|array|min:1',
            'units.*.unit_id' => 'required|exists:units,id',
            'units.*.price' => 'required|numeric|min:0',
            'units.*.conversion_rate' => 'required|numeric|min:0.0001',
            'units.*.is_base_unit' => 'boolean',
        ]);

        // Validasi hanya ada satu base unit
        $baseUnits = collect($request->units)->where('is_base_unit', true);
        if ($baseUnits->count() !== 1) {
            return back()->withErrors(['units' => 'Harus ada tepat satu satuan dasar']);
        }

        try {
            DB::transaction(function () use ($request, $product) {
                $product->update([
                    'name' => $request->name,
                    'barcode' => $request->barcode,
                    'category_id' => $request->category_id,
                    'description' => $request->description,
                    'stock_alert_minimum' => $request->stock_alert_minimum,
                ]);

                // Hapus unit lama
                $product->productUnits()->delete();

                // Tambah unit baru
                foreach ($request->units as $unitData) {
                    ProductUnit::create([
                        'product_id' => $product->id,
                        'unit_id' => $unitData['unit_id'],
                        'price' => $unitData['price'],
                        'conversion_rate' => $unitData['conversion_rate'],
                        'is_base_unit' => $unitData['is_base_unit'] ?? false,
                    ]);

                    // Buat stock jika belum ada
                    Stock::firstOrCreate([
                        'product_id' => $product->id,
                        'unit_id' => $unitData['unit_id'],
                    ], [
                        'quantity' => 0,
                    ]);
                }
            });

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui produk: ' . $e->getMessage()]);
        }
    }

    public function destroy(Product $product)
    {
        try {
            // Cek apakah produk pernah digunakan dalam transaksi
            if ($product->transactionDetails()->exists()) {
                return back()->withErrors(['error' => 'Produk tidak dapat dihapus karena sudah pernah digunakan dalam transaksi']);
            }

            $product->delete();

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil dihapus');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus produk: ' . $e->getMessage()]);
        }
    }

    public function toggle(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return response()->json([
            'success' => true,
            'message' => "Produk berhasil {$status}",
            'is_active' => $product->is_active
        ]);
    }
    public function show(Product $product)
    {
        $product->load(['category', 'productUnits.unit', 'stocks.unit', 'tieredPrices']);

        return view('products.show', compact('product'));
    }
}