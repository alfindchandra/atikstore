@extends('layouts.app')

@section('title', 'Manajemen Produk')

@push('styles')
    <style>
        /* Custom CSS untuk tampilan mobile responsive */
        @media (max-width: 767px) {
            .product-card-grid {
                display: grid;
                gap: 1.5rem; /* space-y-6 */
            }
            .product-card {
                padding: 1.25rem; /* p-5 */
                border-radius: 0.75rem; /* rounded-xl */
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* shadow-lg */
                background-color: white;
            }
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    
    <!-- Bagian Header Utama -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-700 to-purple-600 tracking-tight">Manajemen Produk</h1>
            <p class="text-gray-500 mt-2 font-medium">Kelola inventori, harga satuan, dan lihat status produk Anda.</p>
        </div>
        <a href="{{ route('products.create') }}" class="w-full md:w-auto group px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 hover:shadow-lg hover:shadow-indigo-200 transition-all duration-200 flex items-center justify-center transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2 transform group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Produk Baru
        </a>
    </div>

    <!-- Toolbar Filter & Pencarian (Backend Sync) -->
    <div class="bg-white rounded-xl shadow-md p-5 mb-6 border border-gray-100 relative z-10">
        <form method="GET" action="{{ route('products.index') }}" id="filter-form" class="flex flex-col md:flex-row md:items-end gap-4 md:gap-5">
            
            <div class="w-full md:flex-1">
                <label for="search-input" class="block text-sm font-semibold text-gray-700 mb-1.5"><i class="fas fa-search mr-1 text-indigo-400"></i> Cari Produk</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Ketik nama atau barcode..." 
                           class="block w-full pl-10 pr-3 py-2.5 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
            </div>

            <div class="w-full md:flex-none md:w-48">
                <label for="category-filter" class="block text-sm font-semibold text-gray-700 mb-1.5"><i class="fas fa-tags mr-1 text-indigo-400"></i> Kategori</label>
                <div class="relative">
                    <select id="category-filter" name="category_id" class="block w-full rounded-lg border-gray-300 py-2.5 pl-3 pr-10 text-base focus:border-indigo-500 focus:ring-indigo-500 transition-colors cursor-pointer appearance-none">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <div class="w-full md:flex-none md:w-36">
                <label for="status-filter" class="block text-sm font-semibold text-gray-700 mb-1.5"><i class="fas fa-power-off mr-1 text-green-500"></i> Status</label>
                <div class="relative">
                    <select id="status-filter" name="status" class="block w-full rounded-lg border-gray-300 py-2.5 pl-3 pr-10 text-base focus:border-indigo-500 focus:ring-indigo-500 transition-colors cursor-pointer appearance-none">
                        <option value="">Semua</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <div class="w-full md:flex-none md:w-36">
                <label for="stock-filter" class="block text-sm font-semibold text-gray-700 mb-1.5"><i class="fas fa-box-open mr-1 text-orange-400"></i> Stok Alert</label>
                <div class="relative">
                    <select id="stock-filter" name="stock" class="block w-full rounded-lg border-gray-300 py-2.5 pl-3 pr-10 text-base focus:border-indigo-500 focus:ring-indigo-500 transition-colors cursor-pointer appearance-none">
                        <option value="">Semua</option>
                        <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="normal" {{ request('stock') === 'normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-auto flex space-x-2 mt-2 md:mt-0 items-end">
                @if(request()->anyFilled(['category_id', 'status', 'stock', 'search']))
                <a href="{{ route('products.index') }}" class="w-full md:w-auto px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors text-center shadow-sm">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
                @endif
                <button type="submit" class="hidden md:hidden w-full px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    Terapkan
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Daftar Produk</h2>
            <div class="px-3 py-1 bg-indigo-50 text-indigo-700 text-sm font-bold rounded-full border border-indigo-100 shadow-sm">
                Total: <span id="total-products">{{ $products->total() }}</span> produk
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100" id="products-table">
                    @forelse($products as $product)
                    <tr class="product-row hover:bg-slate-50 transition-colors duration-150"
                        data-category="{{ $product->category_id }}"
                        data-status="{{ $product->is_active ? '1' : '0' }}"
                        data-stock="{{ $product->isLowStock() ? 'low' : 'normal' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center text-indigo-700 font-bold text-lg shadow-sm border border-indigo-50">
                                    {{ substr($product->name, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900 product-name">{{ $product->name }}</div>
                                    @if($product->barcode)
                                    <div class="text-xs text-gray-500 product-barcode mt-0.5 flex items-center">
                                        <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        {{ $product->barcode }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $product->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->productUnits->count() > 0)
                                @php
                                    $baseUnit = $product->productUnits->where('is_base_unit', true)->first();
                                    $minPrice = $product->productUnits->min('price');
                                    $maxPrice = $product->productUnits->max('price');
                                @endphp
                                <div class="text-sm font-bold text-gray-900">
                                @if($minPrice == $maxPrice)
                                    Rp {{ number_format($minPrice, 0, ',', '.') }}
                                @else
                                    Rp {{ number_format($minPrice, 0, ',', '.') }} - {{ number_format($maxPrice, 0, ',', '.') }}
                                @endif
                                </div>
                                @if($baseUnit)
                                    <div class="text-xs text-gray-500 mt-1 flex flex-col font-medium">
                                        <div class="flex items-center mb-0.5">
                                            <span class="bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded mr-1">Base</span> Rp {{ number_format($baseUnit->price, 0, ',', '.') }}/{{ $baseUnit->unit->symbol }}
                                        </div>
                                        @if($baseUnit->enable_tiered_pricing && $baseUnit->tieredPrices->count() > 0)
                                            <div class="text-xs text-orange-600 mt-1">
                                                <i class="fas fa-layer-group mr-1"></i>Ada Harga Grosir
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @else
                                <span class="text-gray-400 font-medium">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">
                                {{ number_format($product->getTotalStockInBaseUnit(), 0, ',', '.') }} 
                                {{ $product->getBaseUnit()->unit->symbol ?? '' }}
                            </div>
                            @if($product->isLowStock())
                            <div class="text-xs text-rose-700 font-bold mt-1.5 inline-flex items-center bg-rose-50 px-2 py-0.5 rounded border border-rose-100">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Stok Rendah (Min: {{ number_format($product->stock_alert_minimum, 0, ',', '.') }})
                            </div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 p-2 rounded-lg transition-colors" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="text-emerald-600 hover:text-emerald-900 hover:bg-emerald-50 p-2 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <a href="{{ route('stock.product', $product) }}" class="text-blue-600 hover:text-blue-900 hover:bg-blue-50 p-2 rounded-lg transition-colors" title="Kelola Stok">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block delete-form" data-product-name="{{ $product->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-900 hover:bg-rose-50 p-2 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="mx-auto w-24 h-24 mb-4 bg-gray-50 rounded-full flex items-center justify-center">
                                <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <h3 class="mt-2 text-lg font-bold text-gray-900">Tidak ada produk</h3>
                            <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto">Mulai dengan menambahkan produk pertama Anda untuk mengelola inventaris toko.</p>
                            <div class="mt-6 flex justify-center">
                                <a href="{{ route('products.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-bold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all duration-200">
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

        <div class="md:hidden bg-gray-50 p-4">
            <div class="product-card-grid space-y-4" id="products-card-grid">
                @forelse($products as $product)
                <div class="product-card product-row bg-white rounded-xl shadow-sm border border-gray-100 p-5 transform transition-all duration-200 hover:shadow-md"
                     data-category="{{ $product->category_id }}"
                     data-status="{{ $product->is_active ? '1' : '0' }}"
                     data-stock="{{ $product->isLowStock() ? 'low' : 'normal' }}">
                    <div class="flex items-start justify-between mb-4 border-b border-gray-50 pb-4">
                        <div class="flex items-center">
                            <div class="h-12 w-12 flex-shrink-0 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center text-indigo-700 font-bold text-xl shadow-sm border border-indigo-50 mr-4">
                                {{ substr($product->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-base font-bold text-gray-900 product-name leading-tight">{{ $product->name }}</div>
                                @if($product->barcode)
                                <div class="text-xs text-gray-500 product-barcode mt-1.5 flex items-center font-medium">
                                    <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    {{ $product->barcode }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-5 bg-slate-50 rounded-lg p-3 border border-slate-100">
                        <div>
                            <div class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Harga</div>
                            <div class="font-bold text-gray-900 text-sm">
                                @if($product->productUnits->count() > 0)
                                    @php
                                        $minPrice = $product->productUnits->min('price');
                                        $maxPrice = $product->productUnits->max('price');
                                    @endphp
                                    @if($minPrice == $maxPrice)
                                        Rp {{ number_format($minPrice, 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($minPrice, 0, ',', '.') }}<br>- {{ number_format($maxPrice, 0, ',', '.') }}
                                    @endif
                                    @if(isset($product->getBaseUnit()->enable_tiered_pricing) && $product->getBaseUnit()->enable_tiered_pricing && $product->getBaseUnit()->tieredPrices->count() > 0)
                                        <div class="text-[10px] text-orange-600 mt-1 font-bold">
                                            <i class="fas fa-layer-group mr-1"></i>Ada Grosir
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Stok & Kategori</div>
                            <div class="font-bold text-gray-900 text-sm flex flex-col items-start">
                                <span class="mb-1">{{ number_format($product->getTotalStockInBaseUnit(), 0, ',', '.') }} {{ $product->getBaseUnit()->unit->symbol ?? '' }}</span>
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $product->category->name }}
                                </span>
                                @if($product->isLowStock())
                                <span class="text-[10px] text-rose-700 font-bold mt-1.5 bg-rose-50 px-2 py-0.5 rounded border border-rose-100 flex items-center">⚠️ Rendah</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex space-x-2 w-full">
                            <a href="{{ route('products.show', $product) }}" class="flex-1 flex flex-col items-center justify-center bg-gray-50 text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700 py-2.5 rounded-lg text-xs font-bold transition-colors border border-gray-100" title="Detail">
                                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Detail
                            </a>
                            <a href="{{ route('products.edit', $product) }}" class="flex-1 flex flex-col items-center justify-center bg-gray-50 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 py-2.5 rounded-lg text-xs font-bold transition-colors border border-gray-100" title="Edit">
                                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit
                            </a>
                            <a href="{{ route('stock.product', $product) }}" class="flex-1 flex flex-col items-center justify-center bg-gray-50 text-blue-600 hover:bg-blue-50 hover:text-blue-700 py-2.5 rounded-lg text-xs font-bold transition-colors border border-gray-100" title="Stok">
                                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                Stok
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                    <div class="mx-auto w-20 h-20 mb-4 bg-gray-50 rounded-full flex items-center justify-center">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="mt-2 text-lg font-bold text-gray-900">Tidak ada produk</h3>
                    <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto">Mulai dengan menambahkan produk pertama Anda untuk mengelola inventaris toko.</p>
                    <div class="mt-6 flex justify-center">
                        <a href="{{ route('products.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-bold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
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
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

<div id="loading-overlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white p-5 rounded-2xl shadow-xl flex flex-col items-center">
        <div class="animate-spin rounded-full h-10 w-10 border-4 border-indigo-100 border-t-indigo-600 mb-3"></div>
        <div class="text-sm font-bold text-gray-700">Memuat data...</div>
    </div>
</div>

<div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadingOverlay = document.getElementById('loading-overlay');

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

        // Auto-submit search when typing
        const searchInput = document.getElementById('search-input');
        const filterForm = searchInput.closest('form');
        let searchTimeout;

        if (searchInput && filterForm) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                // Menunggu 500ms setelah user berhenti mengetik sebelum submit otomatis
                searchTimeout = setTimeout(() => {
                    loadingOverlay.classList.remove('hidden');
                    filterForm.submit();
                }, 500);
            });
            
            // Auto submit when filters change
            const filters = ['category-filter', 'status-filter', 'stock-filter'];
            filters.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', () => {
                        loadingOverlay.classList.remove('hidden');
                        filterForm.submit();
                    });
                }
            });
        }
    });
</script>
@endsection