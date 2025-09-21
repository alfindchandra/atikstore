@extends('layouts.app')

@section('title', 'Daftar Produk')

@push('styles')
    <style>
        /* Custom CSS untuk tampilan mobile */
        @media (max-width: 767px) {
            .product-card-grid {
                display: grid;
                gap: 1.5rem; /* space-y-6 */
            }
            .product-card {
                padding: 1.25rem; /* p-5 */
                border-radius: 0.5rem; /* rounded-lg */
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); /* shadow-sm */
                background-color: white;
            }
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">Daftar Produk</h1>
            <p class="text-gray-600 mt-1">Kelola semua produk Anda, lihat stok, dan ubah status dengan mudah.</p>
        </div>
        <a href="{{ route('products.create') }}" class="w-full md:w-auto px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200 flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Produk
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-4 md:space-y-0">
            <div class="w-full md:flex-1">
                <label for="search-input" class="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text" id="search-input" placeholder="Nama produk atau barcode..." 
                           class="block w-full pl-10 pr-3 py-2 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
            </div>

            <div class="w-full md:flex-none md:w-48">
                <label for="category-filter" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select id="category-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach($products->pluck('category')->unique('id') as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:flex-none md:w-48">
                <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div class="w-full md:flex-none md:w-48">
                <label for="stock-filter" class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                <select id="stock-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="low">Stok Rendah</option>
                    <option value="normal">Stok Normal</option>
                </select>
            </div>

            <div class="w-full md:w-auto flex flex-col md:flex-row md:space-x-3 space-y-2 md:space-y-0 mt-2 md:mt-0">
                <button id="apply-filters" class="w-full md:w-auto px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition-colors">
                    Terapkan
                </button>
                <button id="reset-filters" class="w-full md:w-auto px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Daftar Produk</h2>
            <div class="text-sm text-gray-500">
                Total: <span id="total-products" class="font-bold">{{ $products->total() }}</span> produk
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="products-table">
                    @forelse($products as $product)
                    <tr class="product-row hover:bg-gray-50 transition-colors"
                        data-category="{{ $product->category_id }}"
                        data-status="{{ $product->is_active ? '1' : '0' }}"
                        data-stock="{{ $product->isLowStock() ? 'low' : 'normal' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 product-name">{{ $product->name }}</div>
                            @if($product->barcode)
                            <div class="text-sm text-gray-500 product-barcode">{{ $product->barcode }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                {{ $product->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($product->productUnits->count() > 0)
                                @php
                                    $baseUnit = $product->productUnits->where('is_base_unit', true)->first();
                                    $minPrice = $product->productUnits->min('price');
                                    $maxPrice = $product->productUnits->max('price');
                                @endphp
                                @if($minPrice == $maxPrice)
                                    Rp {{ number_format($minPrice, 0, ',', '.') }}
                                @else
                                    Rp {{ number_format($minPrice, 0, ',', '.') }} - {{ number_format($maxPrice, 0, ',', '.') }}
                                @endif
                                @if($baseUnit)
                                    <div class="text-xs text-gray-500 mt-1">Base: Rp {{ number_format($baseUnit->price, 0, ',', '.') }}/{{ $baseUnit->unit->symbol }}</div>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ number_format($product->getTotalStockInBaseUnit(), 2) }} 
                                {{ $product->getBaseUnit()->unit->symbol ?? '' }}
                            </div>
                            @if($product->isLowStock())
                            <div class="text-xs text-red-600 font-medium">
                                Stok Rendah (Min: {{ number_format($product->stock_alert_minimum, 2) }})
                            </div>
                            @endif
                            @if($product->stocks->count() > 1)
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $product->stocks->count() }} satuan
                            </div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <a href="{{ route('stock.product', $product) }}" class="text-green-600 hover:text-green-900" title="Kelola Stok">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block delete-form" data-product-name="{{ $product->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <h3 class="mt-2 text-base font-medium text-gray-900">Tidak ada produk</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan produk pertama Anda.</p>
                            <div class="mt-6">
                                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Produk
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="md:hidden p-4">
            <div class="product-card-grid " id="products-card-grid">
                @forelse($products as $product)
                <div class="product-card product-row border-b border-gray-900 pb-4 mt-10"
                     data-category="{{ $product->category_id }}"
                     data-status="{{ $product->is_active ? '1' : '0' }}"
                     data-stock="{{ $product->isLowStock() ? 'low' : 'normal' }}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-lg font-bold text-gray-900 product-name">{{ $product->name }}</div>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                            {{ $product->category->name }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-600 product-barcode">{{ $product->barcode }}</div>
                    <div class="mt-2 text-sm text-gray-700">
                        Harga:
                        <span class="font-semibold text-gray-900">
                            @if($product->productUnits->count() > 0)
                                @php
                                    $minPrice = $product->productUnits->min('price');
                                    $maxPrice = $product->productUnits->max('price');
                                @endphp
                                @if($minPrice == $maxPrice)
                                    Rp {{ number_format($minPrice, 0, ',', '.') }}
                                @else
                                    Rp {{ number_format($minPrice, 0, ',', '.') }} - {{ number_format($maxPrice, 0, ',', '.') }}
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="mt-1 text-sm text-gray-700">
                        Stok:
                        <span class="font-semibold text-gray-900">
                            {{ number_format($product->getTotalStockInBaseUnit(), 2) }} {{ $product->getBaseUnit()->unit->symbol ?? '' }}
                        </span>
                        @if($product->isLowStock())
                        <span class="text-xs text-red-600 font-medium ml-2">⚠️ Stok Rendah</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between mt-2  pt-2">
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <a href="{{ route('stock.product', $product) }}" class="text-green-600 hover:text-green-900" title="Kelola Stok">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block delete-form" data-product-name="{{ $product->name }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="mt-2 text-base font-medium text-gray-900">Tidak ada produk</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan produk pertama Anda.</p>
                    <div class="mt-6">
                        <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Produk
                        </a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        @if($products->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

<div id="loading-overlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="animate-spin rounded-full h-12 w-12 border-4 border-white border-t-transparent"></div>
</div>

<div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const categoryFilter = document.getElementById('category-filter');
        const statusFilter = document.getElementById('status-filter');
        const stockFilter = document.getElementById('stock-filter');
        const applyFiltersBtn = document.getElementById('apply-filters');
        const resetFiltersBtn = document.getElementById('reset-filters');
        const productRows = document.querySelectorAll('.product-row');
        const totalProducts = document.getElementById('total-products');
        const loadingOverlay = document.getElementById('loading-overlay');
        const productsTable = document.getElementById('products-table');
        const productsCardGrid = document.getElementById('products-card-grid');

        // Fungsi untuk menampilkan notifikasi toast
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const color = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' ? '✅' : '❌';

            toast.className = `${color} text-white px-4 py-3 rounded-md shadow-lg flex items-center space-x-2 animate-fade-in`;
            toast.innerHTML = `<span>${icon}</span><p class="text-sm font-medium">${message}</p>`;
            
            toastContainer.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('animate-fade-out');
                toast.addEventListener('animationend', () => toast.remove());
            }, 3000);
        }

        // Fungsi untuk menerapkan filter
        function applyFilters() {
            loadingOverlay.classList.remove('hidden');
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilter.value;
            const selectedStatus = statusFilter.value;
            const selectedStock = stockFilter.value;
            
            let visibleCount = 0;

            productRows.forEach(row => {
                let shouldShow = true;

                // Filter Pencarian
                if (searchTerm) {
                    const productName = row.querySelector('.product-name').textContent.toLowerCase();
                    const productBarcode = row.querySelector('.product-barcode')?.textContent.toLowerCase() || '';
                    shouldShow = shouldShow && (productName.includes(searchTerm) || productBarcode.includes(searchTerm));
                }

                // Filter Kategori
                if (selectedCategory) {
                    shouldShow = shouldShow && row.dataset.category === selectedCategory;
                }

                // Filter Status
                if (selectedStatus) {
                    shouldShow = shouldShow && row.dataset.status === selectedStatus;
                }

                // Filter Stok
                if (selectedStock) {
                    shouldShow = shouldShow && row.dataset.stock === selectedStock;
                }

                row.classList.toggle('hidden', !shouldShow);
                if (shouldShow) visibleCount++;
            });

            totalProducts.textContent = visibleCount;
            loadingOverlay.classList.add('hidden');
        }

        // Fungsi untuk mereset filter
        function resetFilters() {
            searchInput.value = '';
            categoryFilter.value = '';
            statusFilter.value = '';
            stockFilter.value = '';
            applyFilters();
        }

        // Event listener untuk tombol filter
        applyFiltersBtn.addEventListener('click', applyFilters);
        resetFiltersBtn.addEventListener('click', resetFilters);
        searchInput.addEventListener('input', applyFilters);

        // Event listener untuk toggle status
        document.querySelectorAll('.toggle-status').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const isActive = this.checked;
                
                loadingOverlay.classList.remove('hidden');

                fetch(`/products/${productId}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        is_active: isActive
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal mengubah status.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const row = this.closest('.product-row');
                        row.dataset.status = isActive ? '1' : '0';
                        showToast(data.message, 'success');
                    } else {
                        this.checked = !isActive; // Revert toggle
                        showToast(data.message || 'Gagal mengubah status produk.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.checked = !isActive; // Revert toggle
                    showToast('Terjadi kesalahan saat mengubah status.', 'error');
                })
                .finally(() => {
                    loadingOverlay.classList.add('hidden');
                });
            });
        });

        // Event listener untuk konfirmasi hapus
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const productName = this.dataset.productName;
                if (!confirm(`Apakah Anda yakin ingin menghapus produk "${productName}"? Tindakan ini tidak dapat dibatalkan.`)) {
                    e.preventDefault();
                } else {
                    loadingOverlay.classList.remove('hidden');
                }
            });
        });

        // Tampilkan notifikasi success jika ada
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        // Tampilkan notifikasi error jika ada
        @if($errors->any())
            showToast('{{ $errors->first() }}', 'error');
        @endif
    });
</script>
@endsection