@extends('layouts.app')

@section('title', 'Detail Stok Produk')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Stok Produk</h1>
            <p class="text-gray-600">{{ $product->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('stock.adjustment') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Sesuaikan Stok
            </a>
            <a href="{{ route('stock.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Kembali
            </a>
        </div>
    </div>

    <!-- Product Info -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Informasi Produk</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Produk</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->category->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->barcode ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Minimum Alert</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ number_format($product->stock_alert_minimum, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Stok (Satuan Dasar)</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ number_format($product->getTotalStockInBaseUnit(), 2) }} 
                        {{ $product->getBaseUnit()->unit->symbol ?? '' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        @if($product->isLowStock())
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Stok Rendah
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Normal
                            </span>
                        @endif
                    </dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Stock -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Stok Saat Ini</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Stok</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($product->stocks as $stock)
                    @php
                        $productUnit = $product->productUnits->where('unit_id', $stock->unit_id)->first();
                        $stockValue = $stock->quantity * ($productUnit->price ?? 0);
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $stock->unit->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $stock->unit->symbol }}</div>
                                </div>
                                @if($productUnit && $productUnit->is_base_unit)
                                <span class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Base
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($stock->quantity, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($productUnit)
                                Rp {{ number_format($productUnit->price, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($stockValue, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <th colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total Nilai Stok:</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">
                            @php
                                $totalValue = $product->stocks->sum(function($stock) use ($product) {
                                    $productUnit = $product->productUnits->where('unit_id', $stock->unit_id)->first();
                                    return $stock->quantity * ($productUnit->price ?? 0);
                                });
                            @endphp
                            Rp {{ number_format($totalValue, 0, ',', '.') }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Stock Movement History -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Riwayat Pergerakan Stok</h2>
                <div class="text-sm text-gray-500">
                    20 pergerakan terakhir
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referensi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($product->stockMovements->take(20) as $movement)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $movement->movement_date->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $movement->movement_type == 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $movement->movement_type == 'in' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($movement->quantity, 2) }} {{ $movement->unit->symbol }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $movement->reference_type == 'transaction' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($movement->reference_type) }}
                                </span>
                                @if($movement->reference_id)
                                <span class="ml-2 text-xs text-gray-500">#{{ $movement->reference_id }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                            {{ $movement->notes ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m4-8v12m4-12v12"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pergerakan stok</h3>
                            <p class="mt-1 text-sm text-gray-500">Belum ada pergerakan stok untuk produk ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($product->stockMovements->count() > 20)
        <div class="px-6 py-4 border-t border-gray-200 text-center">
            <a href="{{ route('stock.movement') }}?product={{ $product->id }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                Lihat Semua Pergerakan
            </a>
        </div>
        @endif
    </div>
</div>
@endsection