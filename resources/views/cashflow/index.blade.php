@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">
        <!-- Page Header -->
        <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-indigo-600/10 rounded-2xl blur-3xl"></div>
            <div class="relative bg-white/80 backdrop-blur-sm rounded-2xl border border-white/20 shadow-xl p-6 lg:p-8">
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                    <div class="space-y-2">
                        <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 bg-clip-text text-transparent">
                            Manajemen Keuangan
                        </h1>
                    </div>
                    <a href="{{ route('cashflow.create') }}" 
                       class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 hover:from-blue-700 hover:to-indigo-700">
                        <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Transaksi
                    </a>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
            <!-- Today Income -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-green-500 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-opacity duration-300"></div>
                <div class="relative bg-white/90 backdrop-blur-sm rounded-2xl border border-white/20 shadow-xl p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-600 mb-1">Pemasukan Hari Ini</p>
                                    <p class="text-xl font-bold text-emerald-600">
                                        Rp {{ number_format($todayFlow['income'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-2 h-16 bg-gradient-to-b from-emerald-400 to-emerald-600 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Today Expense -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-rose-400 to-red-500 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-opacity duration-300"></div>
                <div class="relative bg-white/90 backdrop-blur-sm rounded-2xl border border-white/20 shadow-xl p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-600 mb-1">Pengeluaran Hari Ini</p>
                                    <p class="text-xl  font-bold text-rose-600">
                                        Rp {{ number_format($todayFlow['expense'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-2 h-16 bg-gradient-to-b from-rose-400 to-rose-600 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Today Net -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-opacity duration-300"></div>
                <div class="relative bg-white/90 backdrop-blur-sm rounded-2xl border border-white/20 shadow-xl p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-600 mb-1">Net Hari Ini</p>
                                    <p class="text-xl  font-bold {{ $todayFlow['net'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        Rp {{ number_format($todayFlow['net'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-2 h-16 bg-gradient-to-b {{ $todayFlow['net'] >= 0 ? 'from-emerald-400 to-emerald-600' : 'from-rose-400 to-rose-600' }} rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Monthly Net -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-400 to-violet-500 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-opacity duration-300"></div>
                <div class="relative bg-white/90 backdrop-blur-sm rounded-2xl border border-white/20 shadow-xl p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-600 mb-1">Net Bulan Ini</p>
                                    <p class="text-xl font-bold {{ $monthlyFlow['net'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        Rp {{ number_format($monthlyFlow['net'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-2 h-16 bg-gradient-to-b {{ $monthlyFlow['net'] >= 0 ? 'from-emerald-400 to-emerald-600' : 'from-rose-400 to-rose-600' }} rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-100/50 to-slate-100/50 rounded-2xl blur-3xl"></div>
            <div class="relative bg-white/90 backdrop-blur-sm rounded-2xl border border-white/20 shadow-xl p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="searchInput"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-200" 
                            placeholder="Cari deskripsi..."
                            onkeyup="searchTransactions()"
                        >
                    </div>
                    <div>
                        <select id="typeFilter" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-200" onchange="filterTransactions()">
                            <option value="">Semua Tipe</option>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </div>
                    <div>
                        <input 
                            type="date" 
                            id="dateFilter" 
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-200"
                            onchange="filterTransactions()"
                        >
                    </div>
                    <div>
                        <button class="w-full px-4 py-3 bg-gradient-to-r from-gray-600 to-slate-600 text-white font-medium rounded-xl hover:from-gray-700 hover:to-slate-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl" onclick="resetFilters()">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="relative">
    <div class="absolute inset-0 bg-gradient-to-r from-white/50 to-gray-50/50 rounded-2xl blur-3xl"></div>
    <div class="relative bg-white/90 backdrop-blur-sm rounded-2xl border border-white/20 shadow-xl overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50/50 to-white/50">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900">Riwayat Transaksi</h3>
                <div class="flex items-center text-sm text-gray-500 bg-gray-100/50 px-3 py-1 rounded-lg">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="hidden sm:inline">Total: </span>{{ $cashFlows->total() }} transaksi
                </div>
            </div>
        </div>
        
        <!-- Desktop Table View (hidden on mobile) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Referensi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="transactionsBody" class="divide-y divide-gray-100">
                    @forelse($cashFlows as $cashFlow)
                    <tr class="transaction-row hover:bg-gradient-to-r hover:from-blue-50/30 hover:to-indigo-50/30 transition-all duration-200" 
                        data-description="{{ strtolower($cashFlow->description) }}"
                        data-type="{{ $cashFlow->type }}"
                        data-date="{{ $cashFlow->transaction_date->format('Y-m-d') }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $cashFlow->transaction_date->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $cashFlow->transaction_date->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($cashFlow->type === 'income')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800 border border-emerald-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                </svg>
                                Pemasukan
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-rose-100 to-red-100 text-rose-800 border border-rose-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                </svg>
                                Pengeluaran
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $cashFlow->description }}">
                                {{ $cashFlow->description }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold {{ $cashFlow->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $cashFlow->type === 'income' ? '+' : '-' }} Rp {{ number_format($cashFlow->amount, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($cashFlow->reference_type)
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-md">
                                {{ ucfirst($cashFlow->reference_type) }}
                                @if($cashFlow->reference_id)
                                    #{{ $cashFlow->reference_id }}
                                @endif
                            </span>
                            @else
                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-md">Manual</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                @if($cashFlow->reference_type !== 'transaction')
                                <a href="{{ route('cashflow.edit', $cashFlow) }}" 
                                   class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-lg transition-all duration-200" 
                                   title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button onclick="deleteCashFlow({{ $cashFlow->id }})" 
                                        class="text-rose-600 hover:text-rose-800 p-2 hover:bg-rose-50 rounded-lg transition-all duration-200" 
                                        title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @else
                                <span class="text-gray-400 p-2" title="Tidak dapat diedit (dari transaksi)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center space-y-4">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-slate-200 rounded-full flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div class="space-y-2">
                                    <h3 class="text-lg font-medium text-gray-900">Belum ada transaksi keuangan</h3>
                                    <p class="text-gray-500">Mulai dengan menambahkan transaksi pertama Anda</p>
                                </div>
                                <a href="{{ route('cashflow.create') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Tambah Transaksi Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Card View (visible only on mobile/tablet) -->
        <div class="lg:hidden" id="mobileTransactionsBody">
            @forelse($cashFlows as $cashFlow)
            <div class="transaction-row p-4 border-b border-gray-100 hover:bg-gradient-to-r hover:from-blue-50/30 hover:to-indigo-50/30 transition-all duration-200 last:border-b-0" 
                 data-description="{{ strtolower($cashFlow->description) }}"
                 data-type="{{ $cashFlow->type }}"
                 data-date="{{ $cashFlow->transaction_date->format('Y-m-d') }}">
                
                <!-- Mobile Card Header -->
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center space-x-3">
                        <!-- Transaction Type Badge -->
                        @if($cashFlow->type === 'income')
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            </svg>
                        </div>
                        @else
                        <div class="w-10 h-10 bg-gradient-to-br from-rose-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            </svg>
                        </div>
                        @endif
                        
                        <!-- Date and Time -->
                        <div>
                            <div class="text-sm font-semibold text-gray-900">
                                {{ $cashFlow->transaction_date->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $cashFlow->transaction_date->format('H:i') }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Amount (Right aligned) -->
                    <div class="text-right">
                        <div class="text-lg font-bold {{ $cashFlow->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $cashFlow->type === 'income' ? '+' : '-' }} Rp {{ number_format($cashFlow->amount, 0, ',', '.') }}
                        </div>
                        <!-- Type Badge (Mobile) -->
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1 {{ $cashFlow->type === 'income' ? 'bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800 border border-emerald-200' : 'bg-gradient-to-r from-rose-100 to-red-100 text-rose-800 border border-rose-200' }}">
                            {{ $cashFlow->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                        </span>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mb-3">
                    <p class="text-sm text-gray-900 font-medium leading-relaxed">
                        {{ $cashFlow->description }}
                    </p>
                </div>
                
                <!-- Bottom Row: Reference and Actions -->
                <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                    <!-- Reference Info -->
                    <div>
                        @if($cashFlow->reference_type)
                        <span class="inline-flex items-center text-xs text-gray-500 bg-gray-100/80 px-2 py-1 rounded-lg">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ ucfirst($cashFlow->reference_type) }}
                            @if($cashFlow->reference_id) #{{ $cashFlow->reference_id }} @endif
                        </span>
                        @else
                        <span class="inline-flex items-center text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Manual
                        </span>
                        @endif
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        @if($cashFlow->reference_type !== 'transaction')
                        <!-- Edit Button -->
                        <a href="{{ route('cashflow.edit', $cashFlow) }}" 
                           class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-800 rounded-lg transition-all duration-200 text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span class="hidden sm:inline">Edit</span>
                        </a>
                        
                        <!-- Delete Button -->
                        <button onclick="deleteCashFlow({{ $cashFlow->id }})" 
                                class="inline-flex items-center px-3 py-2 bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-800 rounded-lg transition-all duration-200 text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span class="hidden sm:inline">Hapus</span>
                        </button>
                        @else
                        <!-- Locked Indicator -->
                        <div class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-400 rounded-lg text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Terkunci
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <!-- Empty State for Mobile -->
            <div class="p-8 text-center">
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-slate-200 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-medium text-gray-900">Belum ada transaksi keuangan</h3>
                        <p class="text-gray-500 text-sm px-4">Mulai dengan menambahkan transaksi pertama Anda</p>
                    </div>
                    <a href="{{ route('cashflow.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Transaksi
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>


        <!-- Pagination -->
        @if($cashFlows->hasPages())

    <!-- Enhanced Pagination Controls -->
    <div class="bg-white/95 backdrop-blur-sm rounded-2xl border border-gray-200 shadow-xl p-2 sm:p-3 max-w-full overflow-x-auto">
        <div class="flex items-center justify-center space-x-1 sm:space-x-2">
            
            <!-- Previous Button -->
            @if($cashFlows->onFirstPage())
                <button disabled class="pagination-btn flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200">
                    <i class="fas fa-chevron-left text-xs sm:text-sm"></i>
                    <span class="hidden sm:inline ml-2 text-sm">Prev</span>
                </button>
            @else
                <a href="{{ $cashFlows->previousPageUrl() }}" 
                   class="pagination-btn pagination-hover flex items-center justify-center rounded-xl bg-white hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 border border-gray-200 hover:border-indigo-300 shadow-sm hover:shadow-md">
                    <i class="fas fa-chevron-left text-xs sm:text-sm"></i>
                    <span class="hidden sm:inline ml-2 text-sm font-medium">Prev</span>
                </a>
            @endif

            <!-- Mobile: Show current page only for small screens -->
            <div class="flex sm:hidden">
                <div class="pagination-btn flex items-center justify-center rounded-xl pagination-gradient text-white font-bold shadow-lg">
                    {{ $cashFlows->currentPage() }}
                </div>
            </div>

            <!-- Desktop: Show page numbers -->
            <div class="hidden sm:flex items-center space-x-1">
                @if($cashFlows->currentPage() > 3)
                    <a href="{{ $cashFlows->url(1) }}" 
                       class="pagination-btn pagination-hover flex items-center justify-center rounded-xl bg-white hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 border border-gray-200 hover:border-indigo-300 shadow-sm hover:shadow-md">
                        1
                    </a>
                    
                    @if($cashFlows->currentPage() > 4)
                        <span class="px-2 text-gray-500">...</span>
                    @endif
                @endif

                @foreach(range(max(1, $cashFlows->currentPage() - 2), min($cashFlows->lastPage(), $cashFlows->currentPage() + 2)) as $page)
                    @if($page == $cashFlows->currentPage())
                        <div class="pagination-btn flex items-center justify-center rounded-xl pagination-gradient text-white font-bold shadow-lg transform scale-105">
                            {{ $page }}
                        </div>
                    @else
                        <a href="{{ $cashFlows->url($page) }}" 
                           class="pagination-btn pagination-hover flex items-center justify-center rounded-xl bg-white hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 border border-gray-200 hover:border-indigo-300 shadow-sm hover:shadow-md">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                @if($cashFlows->currentPage() < $cashFlows->lastPage() - 2)
                    @if($cashFlows->currentPage() < $cashFlows->lastPage() - 3)
                        <span class="px-2 text-gray-500">...</span>
                    @endif
                    
                    <a href="{{ $cashFlows->url($cashFlows->lastPage()) }}" 
                       class="pagination-btn pagination-hover flex items-center justify-center rounded-xl bg-white hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 border border-gray-200 hover:border-indigo-300 shadow-sm hover:shadow-md">
                        {{ $cashFlows->lastPage() }}
                    </a>
                @endif
            </div>

            <!-- Next Button -->
            @if($cashFlows->hasMorePages())
                <a href="{{ $cashFlows->nextPageUrl() }}" 
                   class="pagination-btn pagination-hover flex items-center justify-center rounded-xl bg-white hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 border border-gray-200 hover:border-indigo-300 shadow-sm hover:shadow-md">
                    <span class="hidden sm:inline mr-2 text-sm font-medium">Next</span>
                    <i class="fas fa-chevron-right text-xs sm:text-sm"></i>
                </a>
            @else
                <button disabled class="pagination-btn flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200">
                    <span class="hidden sm:inline mr-2 text-sm">Next</span>
                    <i class="fas fa-chevron-right text-xs sm:text-sm"></i>
                </button>
            @endif

        </div>
    </div>

    <!-- Enhanced Mobile Page Info -->
    <div class="sm:hidden bg-white/95 backdrop-blur-sm rounded-xl border border-gray-200 shadow-lg px-4 py-2">
        <div class="flex items-center justify-center space-x-4">
            <!-- Page Jump for Mobile -->
            <div class="flex items-center space-x-2">
                <label class="text-xs text-gray-600">Halaman:</label>
                <select onchange="window.location.href=this.value" 
                        class="text-xs border border-gray-300 rounded-lg px-2 py-1 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @for($i = 1; $i <= $cashFlows->lastPage(); $i++)
                        <option value="{{ $cashFlows->url($i) }}" {{ $i == $cashFlows->currentPage() ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
                <span class="text-xs text-gray-600">dari {{ $cashFlows->lastPage() }}</span>
            </div>
        </div>
    </div>

    <!-- Per Page Selector -->
    <div class="bg-white/95 backdrop-blur-sm rounded-xl border border-gray-200 shadow-lg px-4 py-2 sm:px-6 sm:py-3">
        <div class="flex items-center justify-center space-x-3">
            <label class="text-sm text-gray-600">Tampilkan:</label>
            <select onchange="updatePerPage(this.value)" 
                    class="text-sm border border-gray-300 rounded-lg px-3 py-1 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span class="text-sm text-gray-600">per halaman</span>
        </div>
    </div>
</div>
@endif

        <!-- Quick Stats -->
        @if($cashFlows->count() > 0)
        <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-100/50 to-purple-100/50 rounded-2xl blur-3xl"></div>
            <div class="relative bg-white/90 backdrop-blur-sm rounded-2xl border border-white/20 shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50/50 to-purple-50/50">
                    <h3 class="text-xl font-bold text-gray-900">Statistik Keuangan</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Monthly Chart -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-gray-900">Pemasukan vs Pengeluaran Bulan Ini</h4>
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="font-medium text-gray-700">Pemasukan</span>
                                        <span class="font-bold text-emerald-600">
                                            Rp {{ number_format($monthlyFlow['income'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                        <div class="bg-gradient-to-r from-emerald-500 to-green-600 h-full rounded-full transition-all duration-1000 ease-out" 
                                             style="width: {{ $monthlyFlow['income'] > 0 ? ($monthlyFlow['income'] / ($monthlyFlow['income'] + $monthlyFlow['expense'])) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="font-medium text-gray-700">Pengeluaran</span>
                                        <span class="font-bold text-rose-600">
                                            Rp {{ number_format($monthlyFlow['expense'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                        <div class="bg-gradient-to-r from-rose-500 to-red-600 h-full rounded-full transition-all duration-1000 ease-out" 
                                             style="width: {{ $monthlyFlow['expense'] > 0 ? ($monthlyFlow['expense'] / ($monthlyFlow['income'] + $monthlyFlow['expense'])) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Category Breakdown -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-gray-900">Kategori Terpopuler</h4>
                            <div class="space-y-3">
                                @php
                                    $categories = $cashFlows->groupBy('category')->map(function($items) {
                                        return [
                                            'name' => $items->first()->category,
                                            'count' => $items->count(),
                                            'amount' => $items->sum('amount')
                                        ];
                                    })->sortByDesc('count')->take(5);
                                @endphp
                                
                                @foreach($categories as $category)
                                <div class="flex justify-between items-center p-3 bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl border border-gray-100 hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full"></div>
                                        <span class="font-medium text-gray-700">{{ $category['name'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-900">{{ $category['count'] }}x</div>
                                        <div class="text-xs text-gray-500">
                                            Rp {{ number_format($category['amount'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.transaction-row {
    animation: slideInUp 0.3s ease-out;
}

.transaction-row:nth-child(even) {
    animation-delay: 0.1s;
}

.transaction-row:nth-child(odd) {
    animation-delay: 0.2s;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(45deg, #3b82f6, #6366f1);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(45deg, #2563eb, #4f46e5);
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .transaction-row td {
        padding: 12px 16px;
    }
    
    .transaction-row td:nth-child(3),
    .transaction-row td:nth-child(6) {
        display: none;
    }
}
</style>

<script>
// Enhanced search function with debouncing
let searchTimeout;
function searchTransactions() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll('.transaction-row');
        
        rows.forEach((row, index) => {
            const description = row.getAttribute('data-description');
            const shouldShow = description.includes(filter);
            
            if (shouldShow) {
                row.style.display = '';
                row.style.animationDelay = `${index * 0.05}s`;
                row.classList.add('animate-fade-in');
            } else {
                row.style.display = 'none';
                row.classList.remove('animate-fade-in');
            }
        });
        
        updateTransactionCount();
    }, 300);
}

// Enhanced filter function
function filterTransactions() {
    const typeFilter = document.getElementById('typeFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const searchFilter = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.transaction-row');
    
    rows.forEach((row, index) => {
        let show = true;
        
        // Type filter
        if (typeFilter && row.getAttribute('data-type') !== typeFilter) {
            show = false;
        }
        
        // Date filter
        if (dateFilter && row.getAttribute('data-date') !== dateFilter) {
            show = false;
        }
        
        // Search filter
        if (searchFilter && !row.getAttribute('data-description').includes(searchFilter)) {
            show = false;
        }
        
        if (show) {
            row.style.display = '';
            row.style.animationDelay = `${index * 0.05}s`;
            row.classList.add('animate-fade-in');
        } else {
            row.style.display = 'none';
            row.classList.remove('animate-fade-in');
        }
    });
    
    updateTransactionCount();
}

// Reset filters with animation
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('dateFilter').value = '';
    
    const rows = document.querySelectorAll('.transaction-row');
    rows.forEach((row, index) => {
        row.style.display = '';
        row.style.animationDelay = `${index * 0.05}s`;
        row.classList.add('animate-fade-in');
    });
    
    updateTransactionCount();
    
    // Add visual feedback
    const resetButton = event.target;
    const originalText = resetButton.innerHTML;
    resetButton.innerHTML = '<svg class="w-4 h-4 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Resetting...';
    
    setTimeout(() => {
        resetButton.innerHTML = originalText;
    }, 1000);
}

// Update transaction count
function updateTransactionCount() {
    const visibleRows = document.querySelectorAll('.transaction-row[style=""], .transaction-row:not([style*="none"])').length;
    const totalElement = document.querySelector('.text-sm.text-gray-500');
    if (totalElement) {
        totalElement.innerHTML = `<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>Menampilkan: ${visibleRows} dari {{ $cashFlows->total() }} transaksi`;
    }
}

// Enhanced delete function with better UX
function deleteCashFlow(cashFlowId) {
    // Create custom confirm dialog
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-rose-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Hapus Transaksi</h3>
                    <p class="text-sm text-gray-600">Aksi ini tidak dapat dibatalkan</p>
                </div>
            </div>
            <p class="text-gray-700 mb-6">Apakah Anda yakin ingin menghapus transaksi keuangan ini? Data yang dihapus tidak dapat dikembalikan.</p>
            <div class="flex space-x-3">
                <button onclick="this.closest('.fixed').remove()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition-colors font-medium">
                    Batal
                </button>
                <button onclick="confirmDelete(${cashFlowId})" class="flex-1 px-4 py-2 bg-gradient-to-r from-rose-600 to-red-600 text-white rounded-xl hover:from-rose-700 hover:to-red-700 transition-all font-medium">
                    Hapus
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add animation
    requestAnimationFrame(() => {
        modal.classList.add('animate-fade-in');
    });
}

function confirmDelete(cashFlowId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/cashflow/${cashFlowId}`;
    form.innerHTML = `
        @csrf
        @method('DELETE')
    `;
    document.body.appendChild(form);
    form.submit();
}

// Auto focus search input and add keyboard shortcuts
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    searchInput.focus();
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + F to focus search
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
        
        // Escape to clear search
        if (e.key === 'Escape') {
            resetFilters();
            searchInput.blur();
        }
    });
    
    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Initialize animation observer for cards
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = '0.1s';
                entry.target.classList.add('animate-slide-up');
            }
        });
    });
    
    document.querySelectorAll('.group').forEach(card => {
        observer.observe(card);
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fade-in 0.3s ease-out forwards;
    }
    
    .animate-slide-up {
        animation: slide-up 0.6s ease-out forwards;
    }
`;
document.head.appendChild(style);
</script>
@endsection