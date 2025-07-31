@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Stok</h1>
            <p class="text-gray-600">Monitoring stok dan nilai inventori</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <a href="{{ route('reports.export.stock', ['format' => 'pdf']) }}" class="btn-primary">
                <i class="fas fa-download mr-2"></i>Export PDF
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Filter</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.stock') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-input">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Status Stok</label>
                    <select name="stock_status" class="form-input">
                        <option value="all" {{ $stockStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="low" {{ $stockStatus === 'low' ? 'selected' : '' }}>Stok Menipis</option>
                        <option value="normal" {{ $stockStatus === 'normal' ? 'selected' : '' }}>Stok Normal</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-boxes text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Produk</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ number_format($stockValue['total_products'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Nilai Inventori</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            Rp {{ number_format($stockValue['total_value'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Stok Menipis</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ number_format($stockValue['low_stock_count'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="card">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Detail Stok Produk</h3>
                <div class="text-sm text-gray-500">
                    Menampilkan {{ $products->count() }} produk
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Stok per Satuan</th>
                            <th>Stok Minimum</th>
                            <th>Nilai Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                    @if($product->barcode)
                                    <p class="text-sm text-gray-500">{{ $product->barcode }}</p>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $product->category->name ?? 'Tidak ada' }}</span>
                            </td>
                            <td>
                                <div class="text-sm space-y-1">
                                    @foreach($product->stocks as $stock)
                                    <div class="flex justify-between">
                                        <span>{{ $stock->unit->name ?? 'Unknown' }}:</span>
                                        <span class="font-medium">{{ number_format($stock->quantity, 0, ',', '.') }}</span>
                                    </div>
                                    @endforeach
                                    @if($product->stocks->isEmpty())
                                    <span class="text-gray-500">Tidak ada stok</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                    $baseUnit = $product->getBaseUnit();
                                @endphp
                                <span class="text-sm">
                                    {{ number_format($product->stock_alert_minimum, 0, ',', '.') }}
                                    {{ $baseUnit ? $baseUnit->unit->symbol : 'unit' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $totalValue = 0;
                                    foreach ($product->stocks as $stock) {
                                        $productUnit = $product->productUnits->where('unit_id', $stock->unit_id)->first();
                                        if ($productUnit) {
                                            $totalValue += $stock->quantity * $productUnit->price;
                                        }
                                    }
                                @endphp
                                <span class="font-medium">Rp {{ number_format($totalValue, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                @if($product->isLowStock())
                                    <span class="badge badge-warning">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Menipis
                                    </span>
                                @else
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle mr-1"></i>Normal
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <a href="{{ route('stock.product', $product) }}" 
                                       class="text-blue-600 hover:text-blue-800" title="Detail Stok">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}" 
                                       class="text-green-600 hover:text-green-800" title="Edit Produk">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="text-gray-500">
                                    <i class="fas fa-boxes text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">Tidak ada produk</p>
                                    <p class="text-sm">Belum ada produk yang terdaftar</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    @if($stockValue['low_stock_count'] > 0)
    <div class="card border-yellow-200 bg-yellow-50">
        <div class="card-header bg-yellow-100">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                <h3 class="text-lg font-semibold text-yellow-800">Peringatan Stok Menipis</h3>
            </div>
        </div>
        <div class="card-body">
            <p class="text-yellow-700 mb-4">
                Terdapat {{ $stockValue['low_stock_count'] }} produk dengan stok menipis yang memerlukan perhatian segera.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($products->filter(fn($p) => $p->isLowStock()) as $product)
                <div class="bg-white border border-yellow-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-900">{{ $product->name }}</p>
                            <p class="text-sm text-gray-600">{{ $product->category->name ?? 'Tidak ada kategori' }}</p>
                        </div>
                        <div class="text-right">
                            @php
                                $baseUnit = $product->getBaseUnit();
                                $totalStock = $product->getTotalStockInBaseUnit();
                            @endphp
                            <p class="text-sm font-medium text-red-600">
                                {{ number_format($totalStock, 0, ',', '.') }} {{ $baseUnit ? $baseUnit->unit->symbol : 'unit' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Min: {{ number_format($product->stock_alert_minimum, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 flex space-x-3">
                <a href="{{ route('stock.adjustment') }}" class="btn-warning">
                    <i class="fas fa-plus mr-2"></i>Tambah Stok
                </a>
                <a href="{{ route('products.index') }}" class="btn-secondary">
                    <i class="fas fa-cog mr-2"></i>Atur Minimum Stok
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection