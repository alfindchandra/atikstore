
@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Ringkasan aktivitas toko hari ini</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500">Terakhir diperbarui</p>
            <p class="text-lg font-semibold text-gray-900">{{ now()->format('H:i:s') }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today Sales -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Penjualan Hari Ini</p>
                        <p class="text-2xl font-semibold text-gray-900" id="today-sales">
                            {{ number_format($stats['today_sales'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="text-green-600 font-medium">
                            <i class="fas fa-arrow-up mr-1"></i>
                            {{ $stats['today_transactions'] }} transaksi
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Sales -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Penjualan Bulan Ini</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ number_format($stats['monthly_sales'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="text-blue-600 font-medium">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ now()->format('F Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
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
                            {{ $stats['low_stock_products'] }}
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <a href="{{ route('stock.index') }}" class="text-yellow-600 font-medium hover:text-yellow-700">
                            <i class="fas fa-eye mr-1"></i>
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Income Today -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-wallet text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Keuntungan Hari Ini</p>
                        <p class="text-2xl font-semibold {{ $stats['today_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($stats['today_net'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <span class="mr-3">
                            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                            {{ number_format($stats['today_income'], 0, ',', '.') }}
                        </span>
                        <span>
                            <i class="fas fa-arrow-down text-red-500 mr-1"></i>
                            {{ number_format($stats['today_expense'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Grafik Penjualan 7 Hari Terakhir</h3>
            </div>
            <div class="card-body">
                <canvas id="salesChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Produk Terlaris</h3>
            </div>
            <div class="card-body">
                <div class="space-y-4">
                    @forelse($topProducts as $product)
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $product['product_name'] }}</p>
                            <p class="text-xs text-gray-500">
                                {{ number_format($product['total_quantity'], 0, ',', '.') }} {{ $product['unit_symbol'] }} terjual
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($product['total_revenue'], 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $product['transaction_count'] }} transaksi</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">Belum ada data penjualan</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions and Low Stock -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Transaksi Terakhir</h3>
                    <a href="{{ route('pos.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="space-y-4">
                    @forelse($recentTransactions as $transaction)
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $transaction->transaction_number }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $transaction->transaction_date->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </p>
                            <span class="badge badge-success">{{ ucfirst($transaction->status) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-receipt text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">Belum ada transaksi hari ini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="card">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Stok Menipis</h3>
                    <a href="{{ route('stock.index') }}" class="text-yellow-600 hover:text-yellow-700 text-sm font-medium">
                        Kelola Stok
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="space-y-4">
                    @forelse($lowStockProducts as $product)
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $product['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $product['category'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-red-600">
                                {{ number_format($product['current_stock'], 0, ',', '.') }} {{ $product['base_unit'] }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Min: {{ number_format($product['minimum_stock'], 0, ',', '.') }} {{ $product['base_unit'] }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-green-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">Semua stok aman</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('pos.index') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fas fa-cash-register text-2xl text-blue-600 mb-2"></i>
                    <span class="text-sm font-medium text-blue-900">Buka Kasir</span>
                </a>
                
                <a href="{{ route('products.create') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fas fa-plus-circle text-2xl text-green-600 mb-2"></i>
                    <span class="text-sm font-medium text-green-900">Tambah Produk</span>
                </a>
                
                <a href="{{ route('stock.adjustment') }}" class="flex flex-col items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                    <i class="fas fa-warehouse text-2xl text-yellow-600 mb-2"></i>
                    <span class="text-sm font-medium text-yellow-900">Atur Stok</span>
                </a>
                
                <a href="{{ route('cashflow.create') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <i class="fas fa-money-bill-wave text-2xl text-purple-600 mb-2"></i>
                    <span class="text-sm font-medium text-purple-900">Catat Keuangan</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = @json($salesChart);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.map(item => item.formatted_date),
            datasets: [{
                label: 'Penjualan (Rp)',
                data: salesData.map(item => item.total),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            elements: {
                point: {
                    radius: 4,
                    hoverRadius: 6
                }
            }
        }
    });

    // Auto refresh dashboard every 5 minutes
    setInterval(function() {
        location.reload();
    }, 300000);
});
</script>
@endsection