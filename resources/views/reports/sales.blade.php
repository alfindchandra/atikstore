@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="space-y-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Laporan Penjualan</h1>
            <p class="text-md text-gray-500 mt-1">Detail transaksi dan analisis penjualan</p>
        </div>
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-3">
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            
            <div class="relative">
                <button onclick="toggleExportMenu()" class="btn btn-primary">
                    <i class="fas fa-download mr-2"></i>Export
                    <i class="fas fa-chevron-down ml-2"></i>
                </button>
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-20 overflow-hidden">
                    <a href="{{ route('reports.export.sales', ['format' => 'pdf', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" 
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-file-pdf mr-2 text-red-500"></i>Export PDF
                    </a>
                    <a href="{{ route('reports.export.sales', ['format' => 'excel', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" 
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-file-excel mr-2 text-green-500"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <hr class="border-gray-200">

    <div class="card p-6 shadow-md rounded-xl">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Filter Periode</h3>
        <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="form-label" for="date_from">Dari Tanggal</label>
                <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" class="form-input">
            </div>
            <div>
                <label class="form-label" for="date_to">Sampai Tanggal</label>
                <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" class="form-input">
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn btn-primary w-full">
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

        <!-- Transactions List -->
<!-- Transactions List -->
<div class="card">
    <div class="card-header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Transaksi</h3>
            <p class="text-sm text-gray-500">
                Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} transaksi
            </p>
        </div>
    </div>

    <div class="card-body p-0">
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full table-auto text-sm md:text-base text-gray-700">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3">No. Transaksi</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Item</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Pembayaran</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="px-4 py-3 font-medium">{{ $transaction->transaction_number }}</td>
                        <td class="px-4 py-3">{{ $transaction->transaction_date->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            @foreach($transaction->details->take(2) as $detail)
                                <div>{{ $detail->product->name }} ({{ $detail->quantity }} {{ $detail->unit->symbol }})</div>
                            @endforeach
                            @if($transaction->details->count() > 2)
                                <div class="text-gray-500 text-xs">+{{ $transaction->details->count() - 2 }} item lainnya</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-green-600">
                            Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right text-blue-600">
                            Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded-full bg-green-100 text-green-700 text-xs px-2 py-1">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2 text-lg">
                                <button onclick="viewTransaction({{ $transaction->id }})" class="hover:text-blue-600" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editTransaction({{ $transaction->id }})" class="hover:text-green-600" title="Edit Transaksi">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="{{ route('pos.receipt', $transaction) }}" target="_blank" class="hover:text-purple-600" title="Cetak Struk">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card -->
        <div class="grid grid-cols-1 md:hidden gap-4 p-4">
            @forelse($transactions as $transaction)
            <div class="bg-gray-50 border rounded-lg shadow-sm p-4 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-900">{{ $transaction->transaction_number }}</span>
                    <span class="text-xs text-gray-500">{{ $transaction->transaction_date->format('d/m/Y H:i') }}</span>
                </div>
                <div class="text-sm text-gray-700 space-y-1">
                    @foreach($transaction->details->take(2) as $detail)
                        <div>{{ $detail->product->name }} ({{ $detail->quantity }} {{ $detail->unit->symbol }})</div>
                    @endforeach
                    @if($transaction->details->count() > 2)
                        <div class="text-gray-500 text-xs">+{{ $transaction->details->count() - 2 }} item lainnya</div>
                    @endif
                    <div><span class="font-medium">Total:</span> Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                    <div><span class="font-medium">Bayar:</span> Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</div>
                    <div>
                        <span class="inline-block bg-green-100 text-green-700 text-xs rounded-full px-2 py-1">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 text-lg pt-2 border-t mt-2">
                    <button onclick="viewTransaction({{ $transaction->id }})" class="hover:text-blue-600" title="Lihat Detail">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="editTransaction({{ $transaction->id }})" class="hover:text-green-600" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="{{ route('pos.receipt', $transaction) }}" target="_blank" class="hover:text-purple-600" title="Cetak">
                        <i class="fas fa-print"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-receipt text-3xl mb-3"></i>
                <p class="font-semibold">Tidak ada transaksi</p>
                <p class="text-sm">Belum ada transaksi pada periode yang dipilih</p>
            </div>
            @endforelse
        </div>
    </div>

    @if($transactions->hasPages())
    <div class="card-body border-t">
        <div class="flex justify-center">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>


<!-- Top Products -->
<div class="card">
    <div class="card-header">
        <h3 class="text-lg font-semibold text-gray-900">Produk Terlaris</h3>
    </div>
    <div class="card-body">
        @if($topProducts->count())
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full table-auto text-sm md:text-base text-gray-700">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3 text-right">Jumlah Terjual</th>
                        <th class="px-4 py-3 text-right">Total Pendapatan</th>
                        <th class="px-4 py-3 text-right">Jumlah Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $product)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-900">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center text-gray-400">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div>
                                    <div>{{ $product->product_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $product->unit_symbol }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold">
                            {{ number_format($product->total_quantity, 0, ',', '.') }} {{ $product->unit_symbol }}
                        </td>
                        <td class="px-4 py-3 text-right text-green-600 font-semibold">
                            Rp {{ number_format($product->total_revenue, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            {{ $product->transaction_count }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card Version -->
        <div class="grid grid-cols-1 md:hidden gap-4">
            @foreach($topProducts as $product)
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm border">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-10 h-10 bg-white rounded-md flex items-center justify-center text-gray-400 border">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $product->product_name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->unit_symbol }}</p>
                    </div>
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                    <div><span class="font-medium text-gray-800">Jumlah:</span> {{ number_format($product->total_quantity, 0, ',', '.') }} {{ $product->unit_symbol }}</div>
                    <div><span class="font-medium text-gray-800">Pendapatan:</span> Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</div>
                    <div><span class="font-medium text-gray-800">Transaksi:</span> {{ $product->transaction_count }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-inbox text-4xl mb-3"></i>
            <p class="text-lg font-medium">Tidak ada data produk</p>
            <p class="text-sm">Belum ada transaksi produk terlaris pada periode ini</p>
        </div>
        @endif
    </div>
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
    fetch(`reports/transactions/${transactionId}`)
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
    window.location.href = `reports/transactions/${transactionId}/edit`;
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

<style>


    /* Styling Dasar untuk Komponen */
    .btn {
        @apply inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg font-semibold text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2;
    }
    .btn-primary {
        @apply bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500;
    }
    .btn-secondary {
        @apply bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-400;
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
</style>
@endsection