@extends('layouts.app')

@section('content')
<div class="space-y-6 p-4 md:p-8 bg-gray-50 min-h-screen">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Laporan</h1>
            <p class="text-md text-gray-500 mt-1">Analisis dan ringkasan data bisnis Anda</p>
        </div>
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-3">
            <a href="{{ route('reports.sales') }}" class="btn btn-primary">
                <i class="fas fa-chart-line mr-2"></i>Penjualan
            </a>
            <a href="{{ route('stock.index') }}" class="btn btn-secondary">
                <i class="fas fa-warehouse mr-2"></i>Stok
            </a>
            <a href="{{ route('cashflow.index') }}" class="btn btn-success">
                <i class="fas fa-money-bill-wave mr-2"></i>Keuangan
            </a>
        </div>
    </div>

    <hr class="border-gray-200">

    <div class="card shadow-md">
        <div class="card-header bg-white rounded-t-lg p-5">
            <h3 class="text-xl font-semibold text-gray-800">Filter Laporan</h3>
        </div>
        <div class="card-body bg-white rounded-b-lg p-5">
            <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                <div class="col-span-1">
                    <label class="form-label" for="date_from">Dari Tanggal</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" class="form-input">
                </div>
                <div class="col-span-1">
                    <label class="form-label" for="date_to">Sampai Tanggal</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" class="form-input">
                </div>
                <div class="col-span-1">
                    <label class="form-label" for="report_type">Jenis Laporan</label>
                    <select id="report_type" name="report_type" class="form-input">
                        <option value="sales" {{ $reportType === 'sales' ? 'selected' : '' }}>Penjualan</option>
                        <option value="cashflow" {{ $reportType === 'cashflow' ? 'selected' : '' }}>Arus Kas</option>
                    </select>
                </div>
                <div class="col-span-1 flex items-end">
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="card p-6 shadow-md rounded-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Penjualan</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-blue-600 font-semibold">
                {{ $stats['total_transactions'] }} transaksi
            </div>
        </div>

        <div class="card p-6 shadow-md rounded-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Rata-rata Transaksi</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        Rp {{ number_format($stats['average_transaction'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-receipt text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-500">
                <span>Per transaksi</span>
            </div>
        </div>

        <div class="card p-6 shadow-md rounded-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Laba Bersih</p>
                    <p class="text-3xl font-bold mt-1 {{ $stats['net_income'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($stats['net_income'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex flex-col sm:flex-row sm:items-center text-sm">
                <div class="flex items-center text-green-500 mr-4">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span>Rp {{ number_format($stats['total_income'], 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center text-red-500 mt-2 sm:mt-0">
                    <i class="fas fa-arrow-down mr-1"></i>
                    <span>Rp {{ number_format($stats['total_expense'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-md">
        <div class="card-header bg-white rounded-t-lg p-5">
            <h3 class="text-xl font-semibold text-gray-800">
                Grafik {{ $reportType === 'sales' ? 'Penjualan' : 'Arus Kas' }}
            </h3>
        </div>
        <div class="card-body bg-white rounded-b-lg p-5">
            <div class="h-80">
                <canvas id="reportChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card p-6 shadow-md rounded-xl hover:shadow-lg transition-shadow duration-300 cursor-pointer" onclick="window.location.href='{{ route('reports.sales') }}'">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900">Laporan Penjualan</h4>
                    <p class="text-gray-500 text-sm mt-1">Detail transaksi dan analisis penjualan</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-blue-600 font-medium">
                <span>Lihat Detail</span>
                <i class="fas fa-arrow-right ml-2 transition-transform duration-300 transform group-hover:translate-x-1"></i>
            </div>
        </div>

        <div class="card p-6 shadow-md rounded-xl hover:shadow-lg transition-shadow duration-300 cursor-pointer" onclick="window.location.href='{{ route('stock.index') }}'">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900">Laporan Stok</h4>
                    <p class="text-gray-500 text-sm mt-1">Monitoring stok dan nilai inventori</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-warehouse text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-yellow-600 font-medium">
                <span>Lihat Detail</span>
                <i class="fas fa-arrow-right ml-2 transition-transform duration-300 transform group-hover:translate-x-1"></i>
            </div>
        </div>

        <div class="card p-6 shadow-md rounded-xl hover:shadow-lg transition-shadow duration-300 cursor-pointer" onclick="window.location.href='{{ route('cashflow.index') }}'">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900">Laporan Keuangan</h4>
                    <p class="text-gray-500 text-sm mt-1">Arus kas masuk dan keluar</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-green-600 font-medium">
                <span>Lihat Detail</span>
                <i class="fas fa-arrow-right ml-2 transition-transform duration-300 transform group-hover:translate-x-1"></i>
            </div>
        </div>
    </div>

    <div class="card shadow-md">
        <div class="card-header bg-white rounded-t-lg p-5">
            <h3 class="text-xl font-semibold text-gray-800">Perbandingan Periode</h3>
        </div>
        <div class="card-body bg-white rounded-b-lg p-5">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="py-3 px-6">Periode</th>
                            <th scope="col" class="py-3 px-6">Total Penjualan</th>
                            <th scope="col" class="py-3 px-6">Jumlah Transaksi</th>
                            <th scope="col" class="py-3 px-6">Rata-rata Transaksi</th>
                            <th scope="col" class="py-3 px-6">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ date('d/m/Y', strtotime($dateFrom)) }} - {{ date('d/m/Y', strtotime($dateTo)) }}
                            </td>
                            <td class="py-4 px-6">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</td>
                            <td class="py-4 px-6">{{ $stats['total_transactions'] }}</td>
                            <td class="py-4 px-6">Rp {{ number_format($stats['average_transaction'], 0, ',', '.') }}</td>
                            <td class="py-4 px-6">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">Periode Aktif</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
                        backgroundColor: 'rgba(31, 41, 55, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 8,
                        padding: 12,
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
                            color: '#6B7280',
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#E5E7EB'
                        },
                        ticks: {
                            color: '#6B7280',
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            },
                            font: {
                                size: 12
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
<style>
    /* Styling Tambahan untuk Komponen Modern */
    .btn {
        @apply inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg font-semibold text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2;
    }

    .btn-primary {
        @apply bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500;
    }

    .btn-secondary {
        @apply bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-400;
    }

    .btn-success {
        @apply bg-green-600 text-white hover:bg-green-700 focus:ring-green-500;
    }

    .card {
        @apply bg-white rounded-xl shadow-md overflow-hidden;
    }

    .form-label {
        @apply block text-sm font-medium text-gray-700 mb-1;
    }

    .form-input {
        @apply block w-full px-4 py-2 mt-1 text-gray-900 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500;
    }

    .table th, .table td {
        @apply text-sm py-4 px-6;
    }
</style>
@endsection