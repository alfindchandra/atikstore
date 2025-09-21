@extends('layouts.app')

@section('title', 'Manajemen Stok')

@push('styles')
    <style>
        /* Custom CSS untuk tampilan mobile */
        @media (max-width: 767px) {
            .stock-card-grid {
                display: grid;
                gap: 1.5rem; /* space-y-6 */
            }
            .stock-card {
                padding: 1.5rem; /* p-6 */
                border-radius: 0.75rem; /* rounded-xl */
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* shadow-lg */
                background-color: white;
            }
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">Manajemen Stok</h1>
            <p class="text-gray-600 mt-1">Kelola stok produk, penyesuaian, dan riwayat pergerakan.</p>
        </div>
        <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-3">
            <a href="{{ route('stock.adjustment') }}" class="w-full md:w-auto px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200 text-center">
                Penyesuaian Stok
            </a>
            <a href="{{ route('stock.movement') }}" class="w-full md:w-auto px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 text-center">
                Riwayat Pergerakan
            </a>
        </div>
    </div>

    @if($lowStockProducts->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6 shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3 w-full">
                <h3 class="text-lg font-bold text-red-800">⚠️ Peringatan Stok Rendah</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>{{ $lowStockProducts->count() }} produk memiliki stok di bawah minimum alert:</p>
                    <ul class="mt-2 list-disc list-inside space-y-1">
                        @foreach($lowStockProducts->take(3) as $product)
                        <li>
                            <span class="font-semibold">{{ $product->name }}</span> ({{ number_format($product->getTotalStockInBaseUnit(), 2) }} {{ $product->getBaseUnit()->unit->symbol ?? '' }})
                        </li>
                        @endforeach
                        @if($lowStockProducts->count() > 3)
                        <li>Dan {{ $lowStockProducts->count() - 3 }} produk lainnya...</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center justify-between space-y-3 md:space-y-0">
            <h2 class="text-xl font-semibold text-gray-900">Daftar Stok Produk</h2>
            <div class="w-full md:w-auto relative rounded-md shadow-sm">
                <input type="text" id="search" placeholder="Cari produk..." 
                       class="block w-full pl-10 pr-3 py-2 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok per Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Alert</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="products-table">
                    @forelse($products as $product)
                    <tr class="product-row hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 product-name">{{ $product->name }}</div>
                            @if($product->barcode)
                            <div class="text-sm text-gray-500">{{ $product->barcode }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->category->name }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                @foreach($product->stocks as $stock)
                                <div class="text-sm text-gray-900">
                                    {{ number_format($stock->quantity, 2) }} {{ $stock->unit->symbol }}
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($product->getTotalStockInBaseUnit(), 2) }} 
                            {{ $product->getBaseUnit()->unit->symbol ?? '' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($product->stock_alert_minimum, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->isLowStock())
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Stok Rendah
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Normal
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('stock.product', $product) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Detail
                                </a>
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('stock.edit', $product) }}" class="text-green-600 hover:text-green-900">
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <h3 class="mt-2 text-base font-medium text-gray-900">Tidak ada data stok</h3>
                            <p class="mt-1 text-sm text-gray-500">Tambahkan produk untuk mulai mengelola stok.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="md:hidden p-4">
    <div class="space-y-4"> 
        @forelse($products as $product)
        <div class="stock-card product-row border-b border-gray-900 pb-4 last:border-b-0 hover:bg-gray-50 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <div class="text-lg font-bold text-gray-900 product-name">{{ $product->name }}</div>
                <span class="text-xs font-medium text-gray-500">{{ $product->category->name }}</span>
            </div>

            @if($product->barcode)
            <div class="text-sm text-gray-600">{{ $product->barcode }}</div>
            <div class="mt-4 pt-4 flex justify-end space-x-3 text-sm font-medium border-t border-gray-200">
                <a href="{{ route('stock.product', $product) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors">Detail</a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('stock.edit', $product) }}" class="text-green-600 hover:text-green-900 transition-colors">Edit</a>
            </div>
            @endif
            
            <div class="mt-4 pt-4">
                <div class="flex items-center justify-between text-sm text-gray-700">
                    <span class="font-medium">Total Stok:</span>
                    <span class="font-semibold text-gray-900">
                        {{ number_format($product->getTotalStockInBaseUnit(), 2) }} {{ $product->getBaseUnit()->unit->symbol ?? '' }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm text-gray-700 mt-2">
                    <span class="font-medium">Min. Alert:</span>
                    <span class="font-semibold text-gray-900">
                        {{ number_format($product->stock_alert_minimum, 2) }} {{ $product->getBaseUnit()->unit->symbol ?? '' }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm text-gray-700 mt-2">
                    <span class="font-medium">Status:</span>
                    @if($product->isLowStock())
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            Stok Rendah
                        </span>
                    @else
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Normal
                        </span>
                    @endif
                </div>
            </div>

            
        </div>
        @empty
        <div class="px-6 py-12 text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3 class="mt-2 text-base font-medium text-gray-900">Tidak ada data stok</h3>
            <p class="mt-1 text-sm text-gray-500">Tambahkan produk untuk mulai mengelola stok.</p>
        </div>
        @endforelse
    </div>
</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const productRows = document.querySelectorAll('.product-row');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        productRows.forEach(row => {
            const productName = row.querySelector('.product-name').textContent.toLowerCase();
            const shouldShow = productName.includes(searchTerm);
            
            // Menggunakan class `hidden` untuk menyembunyikan elemen
            row.classList.toggle('hidden', !shouldShow);
        });
    });
});
</script>
@endsection