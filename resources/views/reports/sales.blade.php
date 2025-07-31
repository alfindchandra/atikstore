@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Penjualan</h1>
            <p class="text-gray-600">Detail transaksi dan analisis penjualan</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <div class="relative">
                <button onclick="toggleExportMenu()" class="btn-primary">
                    <i class="fas fa-download mr-2"></i>Export
                    <i class="fas fa-chevron-down ml-2"></i>
                </button>
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <a href="{{ route('reports.export.sales', ['format' => 'pdf', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-pdf mr-2 text-red-500"></i>Export PDF
                        </a>
                        <a href="{{ route('reports.export.sales', ['format' => 'excel', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2 text-green-500"></i>Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Filter Periode</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-input">
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
                            <i class="fas fa-money-bill-wave text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
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
                            <i class="fas fa-receipt text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ number_format($summary['total_transactions'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Item Terjual</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ number_format($summary['total_items_sold'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Produk Terlaris</h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah Terjual</th>
                            <th>Total Pendapatan</th>
                            <th>Jumlah Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $product)
                        <tr>
                            <td class="font-medium">{{ $product->product_name }}</td>
                            <td>{{ number_format($product->total_quantity, 0, ',', '.') }} {{ $product->unit_symbol }}</td>
                            <td>Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                            <td>{{ $product->transaction_count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-3"></i>
                                <p>Tidak ada data produk</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="card">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Transaksi</h3>
                <div class="text-sm text-gray-500">
                    Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} transaksi
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="font-medium">{{ $transaction->transaction_number }}</td>
                            <td>{{ $transaction->transaction_date->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="text-sm">
                                    @foreach($transaction->details->take(2) as $detail)
                                        <div>{{ $detail->product->name }} ({{ $detail->quantity }} {{ $detail->unit->symbol }})</div>
                                    @endforeach
                                    @if($transaction->details->count() > 2)
                                        <div class="text-gray-500">+{{ $transaction->details->count() - 2 }} item lainnya</div>
                                    @endif
                                </div>
                            </td>
                            <td class="font-semibold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-success">{{ ucfirst($transaction->status) }}</span>
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <button onclick="viewTransaction({{ $transaction->id }})" 
                                            class="text-blue-600 hover:text-blue-800" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editTransaction({{ $transaction->id }})" 
                                            class="text-green-600 hover:text-green-800" title="Edit Transaksi">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="{{ route('pos.receipt', $transaction) }}" 
                                       class="text-purple-600 hover:text-purple-800" title="Cetak Struk" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="text-gray-500">
                                    <i class="fas fa-receipt text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">Tidak ada transaksi</p>
                                    <p class="text-sm">Belum ada transaksi pada periode yang dipilih</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-body border-t">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Transaction Detail Modal -->
<div id="transactionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Transaksi</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div id="transactionContent" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function toggleExportMenu() {
    const menu = document.getElementById('exportMenu');
    menu.classList.toggle('hidden');
}

// Close export menu when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('exportMenu');
    const button = event.target.closest('button');
    
    if (!button || !button.onclick || button.onclick.toString().indexOf('toggleExportMenu') === -1) {
        menu.classList.add('hidden');
    }
});

function viewTransaction(transactionId) {
    fetch(`/reports/transactions/${transactionId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('transactionContent').innerHTML = html;
            document.getElementById('transactionModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Gagal memuat detail transaksi');
        });
}

function editTransaction(transactionId) {
    window.location.href = `/reports/transactions/${transactionId}/edit`;
}

function closeModal() {
    document.getElementById('transactionModal').classList.add('hidden');
}

// Close modal when clicking backdrop
document.getElementById('transactionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection