@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Laporan</h1>
        <p class="text-gray-600">Lihat berbagai laporan bisnis Anda</p>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Sales Report -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">Laporan Penjualan</h3>
                        <p class="text-sm text-gray-500">Analisis penjualan dan pendapatan</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('reports.sales') }}" class="w-full flex justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <!-- Stock Report -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">Laporan Stok</h3>
                        <p class="text-sm text-gray-500">Status dan pergerakan stok produk</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('reports.stock') }}" class="w-full flex justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <!-- Cash Flow Report -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">Laporan Arus Kas</h3>
                        <p class="text-sm text-gray-500">Pemasukan dan pengeluaran</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('reports.cashflow') }}" class="w-full flex justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700">
                        Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Ringkasan Hari Ini</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">Rp 0</div>
                    <div class="text-sm text-gray-500">Total Penjualan</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">0</div>
                    <div class="text-sm text-gray-500">Transaksi</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">0</div>
                    <div class="text-sm text-gray-500">Produk Terjual</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">0</div>
                    <div class="text-sm text-gray-500">Stok Rendah</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Export Laporan</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-3">Laporan Penjualan</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-md">
                            <span class="text-sm text-gray-700">Export data penjualan ke Excel</span>
                            <a href="{{ route('reports.export.sales') }}?format=excel" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Download Excel
                            </a>
                        </div>
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-md">
                            <span class="text-sm text-gray-700">Export data penjualan ke CSV</span>
                            <a href="{{ route('reports.export.sales') }}?format=csv" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Download CSV
                            </a>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-3">Laporan Stok</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-md">
                            <span class="text-sm text-gray-700">Export data stok ke Excel</span>
                            <a href="{{ route('reports.export.stock') }}?format=excel" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Download Excel
                            </a>
                        </div>
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-md">
                            <span class="text-sm text-gray-700">Export data stok ke CSV</span>
                            <a href="{{ route('reports.export.stock') }}?format=csv" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Download CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection