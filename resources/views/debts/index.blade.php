@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-blue-600">📊 Hutang Pelanggan</h1>
            <p class="text-gray-500 text-sm">Kelola piutang dan pembayaran pelanggan dengan mudah</p>
        </div>
        <a href="{{ route('debts.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 border border-transparent rounded-xl text-sm font-medium text-white hover:bg-blue-700 transition-all duration-200 shadow-sm">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Hutang
        </a>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-600 text-white rounded shadow p-4 flex justify-between">
            <div>
                <p class="text-xs uppercase">Total Piutang</p>
                <p class="text-lg font-bold">Rp {{ number_format($stats['total_debts'], 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-money-bill-wave text-2xl opacity-50"></i>
        </div>
        <div class="bg-sky-500 text-white rounded shadow p-4 flex justify-between">
            <div>
                <p class="text-xs uppercase">Pelanggan Aktif</p>
                <p class="text-lg font-bold">{{ $stats['active_customers'] }}</p>
            </div>
            <i class="fas fa-users text-2xl opacity-50"></i>
        </div>
        <div class="bg-yellow-400 text-gray-900 rounded shadow p-4 flex justify-between">
            <div>
                <p class="text-xs uppercase">Hutang Terlambat</p>
                <p class="text-lg font-bold">Rp {{ number_format($stats['overdue_debts'], 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-exclamation-triangle text-2xl opacity-50"></i>
        </div>
        <div class="bg-green-600 text-white rounded shadow p-4 flex justify-between">
            <div>
                <p class="text-xs uppercase">Total Terbayar</p>
                <p class="text-lg font-bold">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-check-circle text-2xl opacity-50"></i>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded shadow mb-6 p-4">
        <form method="GET" action="{{ route('debts.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-4">
                <label for="statusFilter" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="statusFilter"
                        class="mt-1 p-2 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Sebagian Dibayar</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
            <div class="md:col-span-6">
                <label for="searchInput" class="block text-sm font-medium text-gray-700">Cari Pelanggan</label>
                <input type="text" name="search" id="searchInput" 
                       value="{{ request('search') }}"
                       placeholder="Nama atau nomor telepon..."
                       class="mt-1 block w-full p-2 rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="md:col-span-2 flex items-end">
                <button type="submit" 
                        class="w-full inline-flex justify-center px-3 py-2 border border-blue-600 text-blue-600 rounded hover:bg-blue-50">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel -->
    <div class="hidden md:block overflow-x-auto">
    <table class="w-full text-sm text-left border-collapse">
        <thead class="bg-blue-100 text-blue-700 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 font-semibold">Pelanggan</th>
                <th class="px-4 py-3 font-semibold">Kontak & Alamat</th>
                <th class="px-4 py-3 font-semibold">Tanggal Hutang</th>
                <th class="px-4 py-3 font-semibold text-right">Jumlah</th>
                <th class="px-4 py-3 font-semibold text-right">Sisa</th>
                <th class="px-4 py-3 font-semibold text-center">Status</th>
                <th class="px-4 py-3 font-semibold text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($debts as $debt)
            <tr class="{{ $debt->isOverdue() ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                <td class="p-4 align-top">
                    <div class="font-bold text-gray-800">{{ $debt->customer_name }}</div>
                    @if($debt->transaction)
                        <span class="text-xs text-gray-500 block mt-1">
                            <i class="fas fa-receipt mr-1"></i> Invoice: {{ $debt->transaction->transaction_number }}
                        </span>
                    @endif
                </td>
                <td class="p-4 align-top text-xs text-gray-600">
                    @if($debt->phone)
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gray-400 mr-2"></i> {{ $debt->phone }}
                        </div>
                    @endif
                    @if($debt->address)
                        <div class="flex items-center mt-1">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i> 
                            <span>{{ Str::limit($debt->address, 30) }}</span>
                        </div>
                    @endif
                </td>
                <td class="p-4 align-top">
                    <div>{{ $debt->debt_date->format('d M Y') }}</div>
                    @if($debt->due_date)
                        <div class="text-xs mt-1 {{ $debt->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Jatuh Tempo: {{ $debt->due_date->format('d M Y') }}
                        </div>
                    @endif
                </td>
                <td class="p-4 align-top text-right text-sm font-semibold text-gray-900">
                    Rp {{ number_format($debt->debt_amount, 0, ',', '.') }}
                </td>
                <td class="p-4 align-top text-right text-sm font-semibold {{ $debt->remaining_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                    Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
                </td>
                <td class="p-4 align-top text-center">
                    @switch($debt->status)
                        @case('active') <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Aktif</span> @break
                        @case('partially_paid') <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium">Sebagian</span> @break
                        @case('paid') <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Lunas</span> @break
                        @case('overdue') <span class="bg-red-200 text-red-800 px-3 py-1 rounded-full text-xs font-medium animate-pulse">Terlambat</span> @break
                        @default <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Tidak Diketahui</span>
                    @endswitch
                </td>
                <td class="p-4 align-top">
                    <div class="flex justify-center items-center space-x-2">
                        <a href="{{ route('debts.show', $debt) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if($debt->status !== 'paid')
                        <a href="{{ route('debts.edit', $debt) }}" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                        <form method="POST" action="{{ route('debts.destroy', $debt) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="p-8 text-center text-gray-500">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>Tidak ada data hutang pelanggan.</p>
                    <a href="{{ route('debts.create') }}" 
                       class="inline-block mt-4 px-4 py-2 border border-blue-600 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
                        Tambah Hutang Baru
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="md:hidden">
    <div class="grid grid-cols-1 gap-4">
        @forelse($debts as $debt)
        <div class="bg-white rounded-lg shadow p-4 border {{ $debt->isOverdue() ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            <div class="flex justify-between items-center">
                <h4 class="font-bold text-lg text-gray-800 flex-1">
                    {{ $debt->customer_name }}
                </h4>
                <div class="flex-shrink-0">
                    @switch($debt->status)
                        @case('active') <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Aktif</span> @break
                        @case('partially_paid') <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium">Sebagian</span> @break
                        @case('paid') <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Lunas</span> @break
                        @case('overdue') <span class="bg-red-200 text-red-800 px-3 py-1 rounded-full text-xs font-medium animate-pulse">Terlambat</span> @break
                        @default <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Tidak Diketahui</span>
                    @endswitch
                </div>
            </div>
            
            <hr class="my-3">

            <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                <div class="flex flex-col">
                    <span class="font-semibold text-gray-500">Jumlah Hutang</span>
                    <span class="font-bold text-gray-900">Rp {{ number_format($debt->debt_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold text-gray-500">Sisa Hutang</span>
                    <span class="font-bold {{ $debt->remaining_amount > 0 ? 'text-red-600' : 'text-green-600' }}">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold text-gray-500">Tanggal Hutang</span>
                    <span>{{ $debt->debt_date->format('d M Y') }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold text-gray-500">Jatuh Tempo</span>
                    @if($debt->due_date)
                        <span class="{{ $debt->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                            {{ $debt->due_date->format('d M Y') }}
                        </span>
                    @else
                        <span>-</span>
                    @endif
                </div>
            </div>

            <div class="mt-4 flex justify-between items-center text-xs text-gray-500">
                <div class="flex-1">
                    @if($debt->phone)
                        <div class="flex items-center">
                            <i class="fas fa-phone mr-2"></i> {{ $debt->phone }}
                        </div>
                    @endif
                    @if($debt->transaction)
                        <div class="flex items-center mt-1">
                            <i class="fas fa-receipt mr-2"></i> Invoice: {{ $debt->transaction->transaction_number }}
                        </div>
                    @endif
                </div>
                <div class="flex justify-end space-x-2">
                    <a href="{{ route('debts.show', $debt) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if($debt->status !== 'paid')
                    <a href="{{ route('debts.edit', $debt) }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-edit"></i>
                    </a>
                    @endif
                    <form method="POST" action="{{ route('debts.destroy', $debt) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-inbox fa-2x mb-2"></i>
            <p>Tidak ada data hutang pelanggan.</p>
            <a href="{{ route('debts.create') }}" 
               class="inline-block mt-4 px-4 py-2 border border-blue-600 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
                Tambah Hutang Baru
            </a>
        </div>
        @endforelse
    </div>
</div>

    <!-- Pagination -->
    @if($debts->hasPages())
    <div class="mt-4">
        {{ $debts->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
