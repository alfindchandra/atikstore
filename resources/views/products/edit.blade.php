@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Edit Produk</h2>
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>
    </div>

    <form action="{{ route('products.update', $product) }}" method="POST" class="p-6">
        @csrf
        @method('PATCH')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Informasi Dasar</h3>
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode (Opsional)</label>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('barcode') border-red-500 @enderror">
                    @error('barcode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="category_id" id="category_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('category_id') border-red-500 @enderror">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label for="stock_alert_minimum" class="block text-sm font-medium text-gray-700">Minimum Stok Alert</label>
                    <input type="number" name="stock_alert_minimum" id="stock_alert_minimum" step="0.01" min="0" 
                           value="{{ old('stock_alert_minimum', $product->stock_alert_minimum) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('stock_alert_minimum') border-red-500 @enderror">
                    @error('stock_alert_minimum')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Units -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Satuan & Harga</h3>
                
                <div id="units-container">
                    @foreach($product->productUnits as $index => $productUnit)
                    <div class="unit-row border rounded-lg p-4 bg-gray-50" data-index="{{ $index }}">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-medium text-gray-700">Satuan {{ $index + 1 }}</h4>
                            @if($index > 0)
                                <button type="button" class="text-red-600 hover:text-red-800 remove-unit">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Satuan</label>
                                <select name="units[{{ $index }}][unit_id]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih Satuan</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ $productUnit->unit_id == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ $unit->symbol }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Harga</label>
                                <input type="number" name="units[{{ $index }}][price]" step="0.01" min="0" 
                                       value="{{ $productUnit->price }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Konversi ke Satuan Dasar</label>
                                <input type="number" name="units[{{ $index }}][conversion_rate]" step="0.0001" min="0.0001" 
                                       value="{{ $productUnit->conversion_rate }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="units[{{ $index }}][is_base_unit]" value="1" 
                                       {{ $productUnit->is_base_unit ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label class="ml-2 block text-sm text-gray-700">Satuan Dasar</label>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="button" id="add-unit" class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    + Tambah Satuan
                </button>
            </div>
        </div>

        @error('units')
            <div class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ $message }}
            </div>
        @enderror

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">
                Perbarui Produk
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let unitIndex = {{ $product->productUnits->count() }};
    
    document.getElementById('add-unit').addEventListener('click', function() {
        const container = document.getElementById('units-container');
        const unitRow = createUnitRow(unitIndex);
        container.appendChild(unitRow);
        unitIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-unit')) {
            e.target.closest('.unit-row').remove();
        }
    });

    function createUnitRow(index) {
        const div = document.createElement('div');
        div.className = 'unit-row border rounded-lg p-4 bg-gray-50';
        div.setAttribute('data-index', index);
        
        div.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-medium text-gray-700">Satuan ${index + 1}</h4>
                <button type="button" class="text-red-600 hover:text-red-800 remove-unit">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Satuan</label>
                    <select name="units[${index}][unit_id]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Pilih Satuan</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->symbol }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Harga</label>
                    <input type="number" name="units[${index}][price]" step="0.01" min="0" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Konversi ke Satuan Dasar</label>
                    <input type="number" name="units[${index}][conversion_rate]" step="0.0001" min="0.0001" value="1" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="units[${index}][is_base_unit]" value="1" 
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-700">Satuan Dasar</label>
                </div>
            </div>
        `;
        
        return div;
    }
});
</script>
@endsection