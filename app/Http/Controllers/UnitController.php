<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::withCount('productUnits')->orderBy('name')->get();
        return view('units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
            'symbol' => 'required|string|max:10|unique:units,symbol',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama unit wajib diisi',
            'name.unique' => 'Nama unit sudah ada',
            'symbol.required' => 'Symbol unit wajib diisi',
            'symbol.unique' => 'Symbol unit sudah ada',
        ]);

        try {
            Unit::create($request->all());

            return redirect()->route('units.index')
                ->with('success', 'Unit berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambahkan unit: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
   

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'symbol' => 'required|string|max:10|unique:units,symbol,' . $unit->id,
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama unit wajib diisi',
            'name.unique' => 'Nama unit sudah ada',
            'symbol.required' => 'Symbol unit wajib diisi',
            'symbol.unique' => 'Symbol unit sudah ada',
        ]);

        try {
            $unit->update($request->all());

            return redirect()->route('units.index')
                ->with('success', 'Unit berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui unit: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        try {
            // Cek apakah unit masih digunakan dalam produk
            if ($unit->productUnits()->count() > 0) {
                return back()->withErrors([
                    'error' => 'Unit tidak dapat dihapus karena masih digunakan dalam ' . $unit->productUnits()->count() . ' produk'
                ]);
            }

            // Cek apakah unit masih digunakan dalam stock movements
            if ($unit->stockMovements()->count() > 0) {
                return back()->withErrors([
                    'error' => 'Unit tidak dapat dihapus karena masih memiliki riwayat pergerakan stok'
                ]);
            }

            $unit->delete();

            return redirect()->route('units.index')
                ->with('success', 'Unit berhasil dihapus');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus unit: ' . $e->getMessage()]);
        }
    }
}