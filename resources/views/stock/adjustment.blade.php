@extends('layouts.app')

@section('title', 'Penyesuaian Stok')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Penyesuaian Stok</h2>
                <p class="text-gray-600">Sesuaikan stok produk secara manual</p>
            </div>
            <a href="{{ route('stock.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>
    </div>

    <form action="{{ route('stock.process-adjustment') }}" method="POST" class="p-6">
        @csrf
        
        <!-- Search and Add Product -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Produk</label>
            <div class="flex space-x-3">
                <div class="flex-1 relative">
                    <input type="text" id="product-search" placeholder="Cari produk..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <!-- Search Results Dropdown -->
                    <div id="search-results" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none hidden">
                        <!-- Results will be populated here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Selected Products -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Produk yang Dipilih</h3>
            <div id="selected-products" class="space-y-4">
                <div class="text-gray-500 text-center py-8" id="no-products">
                    Belum ada produk yang dipilih. Gunakan pencarian di atas untuk menambah produk.
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
            <textarea name="notes" id="notes" rows="3" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      placeholder="Alasan penyesuaian stok...">{{ old('notes') }}</textarea>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('stock.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" id="submit-btn" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50" disabled>
                Proses Penyesuaian
            </button>
        </div>
    </form>
</div>
@php
$productsArray = $products->map(function($product) {
    return [
        'id' => $product->id,
        'name' => $product->name,
        'barcode' => $product->barcode,
        'units' => $product->productUnits->map(function($unit) {
            return [
                'unit_id' => $unit->unit_id,
                'unit_name' => $unit->unit->name,
                'unit_symbol' => $unit->unit->symbol,
            ];
        })->toArray(),
    ];
})->toArray();
@endphp
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSearch = document.getElementById('product-search');
    const searchResults = document.getElementById('search-results');
    const selectedProducts = document.getElementById('selected-products');
    const noProducts = document.getElementById('no-products');
    const submitBtn = document.getElementById('submit-btn');
    
    let selectedProductsData = [];
    let productIndex = 0;

    // Product data from backend
   const products = @json($productsArray);

    // Search functionality
    productSearch.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        
        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }

        const filteredProducts = products.filter(product => 
            product.name.toLowerCase().includes(query) || 
            (product.barcode && product.barcode.toLowerCase().includes(query))
        );

        displaySearchResults(filteredProducts);
    });

    function displaySearchResults(products) {
        if (products.length === 0) {
            searchResults.classList.add('hidden');
            return;
        }

        searchResults.innerHTML = products.map(product => `
            <div class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-50" data-product-id="${product.id}">
                <div class="font-medium">${product.name}</div>
                ${product.barcode ? `<div class="text-sm text-gray-500">${product.barcode}</div>` : ''}
            </div>
        `).join('');

        searchResults.classList.remove('hidden');

        // Add click handlers
        searchResults.querySelectorAll('[data-product-id]').forEach(item => {
            item.addEventListener('click', function() {
                const productId = parseInt(this.dataset.productId);
                const product = products.find(p => p.id === productId);
                addProduct(product);
                productSearch.value = '';
                searchResults.classList.add('hidden');
            });
        });
    }

    function addProduct(product) {
        // Check if product already added
        if (selectedProductsData.find(p => p.id === product.id)) {
            alert('Produk sudah ditambahkan');
            return;
        }

        selectedProductsData.push(product);
        renderSelectedProducts();
        updateSubmitButton();
    }

    function renderSelectedProducts() {
        if (selectedProductsData.length === 0) {
            noProducts.style.display = 'block';
            return;
        }

        noProducts.style.display = 'none';
        
        selectedProducts.innerHTML = selectedProductsData.map((product, index) => `
            <div class="border rounded-lg p-4 bg-gray-50">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-medium text-gray-900">${product.name}</h4>
                        ${product.barcode ? `<p class="text-sm text-gray-500">${product.barcode}</p>` : ''}
                    </div>
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="removeProduct(${index})">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${product.units.map(unit => `
                        <div class="flex items-center space-x-3">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">${unit.unit_name} (${unit.unit_symbol})</label>
                                <input type="number" 
                                       name="adjustments[${productIndex}][quantity]" 
                                       step="0.01" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="0.00"
                                       data-product-index="${productIndex}"
                                       data-unit-id="${unit.unit_id}">
                                <input type="hidden" name="adjustments[${productIndex}][product_id]" value="${product.id}">
                                <input type="hidden" name="adjustments[${productIndex}][unit_id]" value="${unit.unit_id}">
                            </div>
                        </div>
                    `).join('')}
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    <p>Masukkan angka positif untuk menambah stok, negatif untuk mengurangi stok</p>
                </div>
            </div>
        `).join('');
        
        productIndex++;
    }

    window.removeProduct = function(index) {
        selectedProductsData.splice(index, 1);
        renderSelectedProducts();
        updateSubmitButton();
    }

    function updateSubmitButton() {
        submitBtn.disabled = selectedProductsData.length === 0;
    }

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#product-search') && !e.target.closest('#search-results')) {
            searchResults.classList.add('hidden');
        }
    });
});
</script>
@endsection