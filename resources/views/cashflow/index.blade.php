@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Keuangan</h1>
            <p class="text-gray-600">Kelola pemasukan dan pengeluaran toko</p>
        </div>
        <a href="{{ route('cashflow.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>
            Tambah Transaksi
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Today Income -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-arrow-up text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pemasukan Hari Ini</p>
                        <p class="text-2xl font-semibold text-green-600">
                            Rp {{ number_format($todayFlow['income'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today Expense -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-arrow-down text-red-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pengeluaran Hari Ini</p>
                        <p class="text-2xl font-semibold text-red-600">
                            Rp {{ number_format($todayFlow['expense'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today Net -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calculator text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Net Hari Ini</p>
                        <p class="text-2xl font-semibold {{ $todayFlow['net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($todayFlow['net'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Net -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Net Bulan Ini</p>
                        <p class="text-2xl font-semibold {{ $monthlyFlow['net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($monthlyFlow['net'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input 
                        type="text" 
                        id="searchInput"
                        class="form-input" 
                        placeholder="Cari deskripsi..."
                        onkeyup="searchTransactions()"
                    >
                </div>
                <div>
                    <select id="typeFilter" class="form-input" onchange="filterTransactions()">
                        <option value="">Semua Tipe</option>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <input 
                        type="date" 
                        id="dateFilter" 
                        class="form-input"
                        onchange="filterTransactions()"
                    >
                </div>
                <div>
                    <button class="btn-secondary w-full" onclick="resetFilters()">
                        <i class="fas fa-undo mr-1"></i>
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Transaksi</h3>
                <div class="text-sm text-gray-500">
                    Total: {{ $cashFlows->total() }} transaksi
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Jumlah</th>
                            <th>Referensi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsBody">
                        @forelse($cashFlows as $cashFlow)
                        <tr class="transaction-row" 
                            data-description="{{ strtolower($cashFlow->description) }}"
                            data-type="{{ $cashFlow->type }}"
                            data-date="{{ $cashFlow->transaction_date->format('Y-m-d') }}">
                            <td>
                                <div class="text-sm text-gray-900">
                                    {{ $cashFlow->transaction_date->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $cashFlow->transaction_date->format('H:i') }}
                                </div>
                            </td>
                            <td>
                                @if($cashFlow->type === 'income')
                                <span class="badge badge-success">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    Pemasukan
                                </span>
                                @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-arrow-down mr-1"></i>
                                    Pengeluaran
                                </span>
                                @endif
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $cashFlow->category }}
                                </span>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">{{ $cashFlow->description }}</div>
                            </td>
                            <td>
                                <div class="text-sm font-medium {{ $cashFlow->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $cashFlow->type === 'income' ? '+' : '-' }} Rp {{ number_format($cashFlow->amount, 0, ',', '.') }}
                                </div>
                            </td>
                            <td>
                                @if($cashFlow->reference_type)
                                <span class="text-xs text-gray-500">
                                    {{ ucfirst($cashFlow->reference_type) }}
                                    @if($cashFlow->reference_id)
                                        #{{ $cashFlow->reference_id }}
                                    @endif
                                </span>
                                @else
                                <span class="text-xs text-gray-400">Manual</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    @if($cashFlow->reference_type !== 'transaction')
                                    <a href="{{ route('cashflow.edit', $cashFlow) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteCashFlow({{ $cashFlow->id }})" 
                                            class="text-red-600 hover:text-red-800 text-sm" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @else
                                    <span class="text-xs text-gray-400" title="Tidak dapat diedit (dari transaksi)">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8">
                                <i class="fas fa-inbox text-gray-400 text-3xl mb-3"></i>
                                <p class="text-gray-500">Belum ada transaksi keuangan</p>
                                <a href="{{ route('cashflow.create') }}" class="btn-primary mt-4">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah Transaksi Pertama
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($cashFlows->hasPages())
    <div class="flex justify-center">
        {{ $cashFlows->links() }}
    </div>
    @endif

    <!-- Quick Stats -->
    @if($cashFlows->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Statistik Keuangan</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Monthly Chart -->
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-4">Pemasukan vs Pengeluaran Bulan Ini</h4>
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Pemasukan</span>
                                <span class="font-medium text-green-600">
                                    Rp {{ number_format($monthlyFlow['income'], 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" 
                                     style="width: {{ $monthlyFlow['income'] > 0 ? ($monthlyFlow['income'] / ($monthlyFlow['income'] + $monthlyFlow['expense'])) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Pengeluaran</span>
                                <span class="font-medium text-red-600">
                                    Rp {{ number_format($monthlyFlow['expense'], 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full" 
                                     style="width: {{ $monthlyFlow['expense'] > 0 ? ($monthlyFlow['expense'] / ($monthlyFlow['income'] + $monthlyFlow['expense'])) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-4">Kategori Terpopuler</h4>
                    <div class="space-y-2">
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
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">{{ $category['name'] }}</span>
                            <div class="text-right">
                                <div class="font-medium">{{ $category['count'] }}x</div>
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
    @endif
</div>

<script>
function searchTransactions() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('.transaction-row');
    
    rows.forEach(row => {
        const description = row.getAttribute('data-description');
        if (description.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function filterTransactions() {
    const typeFilter = document.getElementById('typeFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const rows = document.querySelectorAll('.transaction-row');
    
    rows.forEach(row => {
        let show = true;
        
        if (typeFilter && row.getAttribute('data-type') !== typeFilter) {
            show = false;
        }
        
        if (dateFilter && row.getAttribute('data-date') !== dateFilter) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('dateFilter').value = '';
    
    const rows = document.querySelectorAll('.transaction-row');
    rows.forEach(row => {
        row.style.display = '';
    });
}

function deleteCashFlow(cashFlowId) {
    confirmDelete(() => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/cashflow/${cashFlowId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }, 'Hapus transaksi keuangan ini?');
}

// Auto focus search input
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchInput').focus();
});
</script>
@endsection