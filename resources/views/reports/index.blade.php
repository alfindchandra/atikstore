@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan</h1>
            <p class="text-gray-600">Analisis dan ringkasan data bisnis</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('reports.sales') }}" class="btn-primary">
                <i class="fas fa-chart-line mr-2"></i>Laporan Penjualan
            </a>
            <a href="{{ route('stock.index') }}" class="btn-secondary">
                <i class="fas fa-warehouse mr-2"></i>Laporan Stok
            </a>
            <a href="{{ route('cashflow.index') }}" class="btn-success">
                <i class="fas fa-money-bill-wave mr-2"></i>Laporan Keuangan
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Filter Laporan</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Jenis Laporan</label>
                    <select name="report_type" class="form-input">
                        <option value="sales" {{ $reportType === 'sales' ? 'selected' : '' }}>Penjualan</option>
                        <option value="cashflow" {{ $reportType === 'cashflow' ? 'selected' : '' }}>Arus Kas</option>
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

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Total Sales -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Penjualan</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="text-blue-600 font-medium">
                            {{ $stats['total_transactions'] }} transaksi
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Transaction -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-receipt text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Rata-rata Transaksi</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            Rp {{ number_format($stats['average_transaction'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <span>Per transaksi</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Income -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Laba Bersih</p>
                        <p class="text-2xl font-semibold {{ $stats['net_income'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($stats['net_income'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <span class="mr-3">
                            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                            {{ number_format($stats['total_income'], 0, ',', '.') }}
                        </span>
                        <span>
                            <i class="fas fa-arrow-down text-red-500 mr-1"></i>
                            {{ number_format($stats['total_expense'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">
                Grafik {{ $reportType === 'sales' ? 'Penjualan' : 'Arus Kas' }}
            </h3>
        </div>
        <div class="card-body">
            <div class="h-96">
                <canvas id="reportChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Navigation -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sales Report Card -->
        <div class="card hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('reports.sales') }}'">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">Laporan Penjualan</h4>
                        <p class="text-gray-600 text-sm mt-1">Detail transaksi dan analisis penjualan</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-blue-600 font-medium">
                    <span>Lihat Detail</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>
        </div>

        <!-- Stock Report Card -->
        <div class="card hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('reports.stock') }}'">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">Laporan Stok</h4>
                        <p class="text-gray-600 text-sm mt-1">Monitoring stok dan nilai inventori</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-warehouse text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-yellow-600 font-medium">
                    <span>Lihat Detail</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>
        </div>

        <!-- Cash Flow Report Card -->
        <div class="card hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('reports.cashflow') }}'">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">Laporan Keuangan</h4>
                        <p class="text-gray-600 text-sm mt-1">Arus kas masuk dan keluar</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-green-600 font-medium">
                    <span>Lihat Detail</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Comparison -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Perbandingan Periode</h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th>Total Penjualan</th>
                            <th>Jumlah Transaksi</th>
                            <th>Rata-rata Transaksi</th>
                            <th>Pertumbuhan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-medium">{{ date('d/m/Y', strtotime($dateFrom)) }} - {{ date('d/m/Y', strtotime($dateTo)) }}</td>
                            <td>Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</td>
                            <td>{{ $stats['total_transactions'] }}</td>
                            <td>Rp {{ number_format($stats['average_transaction'], 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-info">Periode Aktif</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart
    const ctx = document.getElementById('reportChart').getContext('2d');
    const chartData = @json($chartData);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.formatted_date),
            datasets: [{
                label: '{{ $reportType === "sales" ? "Penjualan (Rp)" : "Arus Kas (Rp)" }}',
                data: chartData.map(item => item.value),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6B7280'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#F3F4F6'
                    },
                    ticks: {
                        color: '#6B7280',
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: 'rgb(59, 130, 246)'
                }
            }
        }
    });
});
</script>
@endsection