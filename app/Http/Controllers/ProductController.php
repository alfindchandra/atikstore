<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\ProductUnit;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|unique:products,barcode',
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
            DB::transaction(function () use ($request) {
                $product = Product::create([
                    'name' => $request->name,
                    'barcode' => $request->barcode,
                    'category_id' => $request->category_id,
                    'description' => $request->description,
                    'stock_alert_minimum' => $request->stock_alert_minimum,
                ]);

                foreach ($request->units as $unitData) {
                    ProductUnit::create([
                        'product_id' => $product->id,
                        'unit_id' => $unitData['unit_id'], 
                        'price' => $unitData['price'],
                        'conversion_rate' => $unitData['conversion_rate'],
                        'is_base_unit' => $unitData['is_base_unit'] ?? false,
                    ]);

                    // Buat stock awal dengan quantity 0
                    Stock::create([
                        'product_id' => $product->id,
                        'unit_id' => $unitData['unit_id'],
                        'quantity' => 0,
                    ]);
                }
            });

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambahkan produk: ' . $e->getMessage()]);
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'productUnits.unit', 'stocks.unit', 'stockMovements.unit']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['productUnits.unit']);
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
}