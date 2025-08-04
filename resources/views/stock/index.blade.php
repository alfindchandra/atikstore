@extends('layouts.app')

@section('title', 'Manajemen Stok')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Stok</h1>
            <p class="text-gray-600">Kelola stok produk dan lakukan penyesuaian</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('stock.adjustment') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Penyesuaian Stok
            </a>
            <a href="{{ route('stock.movement') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Riwayat Pergerakan
            </a>
        </div>
    </div>

    <!-- Low Stock Alert -->
    @if($lowStockProducts->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800"> 
                    Peringatan Stok Rendah
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>{{ $lowStockProducts->count() }} produk memiliki stok di bawah minimum alert:</p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach($lowStockProducts->take(3) as $product)
                        <li>{{ $product->name }} ({{ number_format($product->getTotalStockInBaseUnit(), 2) }} {{ $product->getBaseUnit()->unit->symbol ?? '' }})</li>
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

    <!-- Stock Table -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Daftar Stok Produk</h2>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <input type="text" id="search" placeholder="Cari produk..." 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
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
                    @foreach($products as $product)
                    <tr class="product-row">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900 product-name">{{ $product->name }}</div>
                                @if($product->barcode)
                                <div class="text-sm text-gray-500">{{ $product->barcode }}</div>
                                @endif
                            </div>
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
                    @endforeach
                </tbody>
            </table>
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
            row.style.display = shouldShow ? '' : 'none';
        });
    });
});
</script>
@endsection