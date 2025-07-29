@extends('layouts.app')

@section('title', 'Riwayat Pergerakan Stok')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Pergerakan Stok</h1>
            <p class="text-gray-600">Lihat semua pergerakan stok produk</p>
        </div>
        <a href="{{ route('stock.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
            Kembali ke Stok
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tipe Pergerakan</label>
                <select id="movement-type-filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="in">Masuk</option>
                    <option value="out">Keluar</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Referensi</label>
                <select id="reference-type-filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="transaction">Transaksi</option>
                    <option value="adjustment">Penyesuaian</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                <input type="date" id="date-from-filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <input type="date" id="date-to-filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
        <div class="mt-4">
            <button id="apply-filters" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Terapkan Filter
            </button>
            <button id="reset-filters" class="ml-2 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Reset
            </button>
        </div>
    </div>

    <!-- Movement Table -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Daftar Pergerakan Stok</h2>
                <div class="text-sm text-gray-500">
                    Total: {{ $movements->total() }} pergerakan
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referensi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($movements as $movement)
                    <tr class="movement-row" 
                        data-movement-type="{{ $movement->movement_type }}"
                        data-reference-type="{{ $movement->reference_type }}"
                        data-date="{{ $movement->movement_date->format('Y-m-d') }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $movement->movement_date->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $movement->product->name }}</div>
                            @if($movement->product->barcode)
                            <div class="text-sm text-gray-500">{{ $movement->product->barcode }}</div>
                            @endif
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
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m4-8v12m4-12v12"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pergerakan stok</h3>
                            <p class="mt-1 text-sm text-gray-500">Belum ada pergerakan stok pada sistem.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $movements->links() }}
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const movementTypeFilter = document.getElementById('movement-type-filter');
    const referenceTypeFilter = document.getElementById('reference-type-filter');
    const dateFromFilter = document.getElementById('date-from-filter');
    const dateToFilter = document.getElementById('date-to-filter');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    const movementRows = document.querySelectorAll('.movement-row');

    function applyFilters() {
        const movementType = movementTypeFilter.value;
        const referenceType = referenceTypeFilter.value;
        const dateFrom = dateFromFilter.value;
        const dateTo = dateToFilter.value;

        movementRows.forEach(row => {
            let shouldShow = true;

            // Filter by movement type
            if (movementType && row.dataset.movementType !== movementType) {
                shouldShow = false;
            }

            // Filter by reference type
            if (referenceType && row.dataset.referenceType !== referenceType) {
                shouldShow = false;
            }

            // Filter by date range
            const rowDate = row.dataset.date;
            if (dateFrom && rowDate < dateFrom) {
                shouldShow = false;
            }
            if (dateTo && rowDate > dateTo) {
                shouldShow = false;
            }

            row.style.display = shouldShow ? '' : 'none';
        });
    }

    function resetFilters() {
        movementTypeFilter.value = '';
        referenceTypeFilter.value = '';
        dateFromFilter.value = '';
        dateToFilter.value = '';
        
        movementRows.forEach(row => {
            row.style.display = '';
        });
    }

    applyFiltersBtn.addEventListener('click', applyFilters);
    resetFiltersBtn.addEventListener('click', resetFilters);

    // Auto-apply filters on change
    [movementTypeFilter, referenceTypeFilter, dateFromFilter, dateToFilter].forEach(filter => {
        filter.addEventListener('change', applyFilters);
    });
});
</script>
@endsection