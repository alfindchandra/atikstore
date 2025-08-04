@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Unit</h1>
            <p class="text-gray-600">Kelola satuan unit produk Anda</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('actions.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <a href="{{ route('units.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Tambah Unit
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-balance-scale text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Unit</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $units->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Unit Digunakan</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ $units->where('product_units_count', '>', 0)->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Unit Tidak Digunakan</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ $units->where('product_units_count', 0)->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Units Table -->
    <div class="card">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Unit</h3>
                <div class="flex items-center space-x-2">
                    <input type="text" 
                           id="search" 
                           placeholder="Cari unit..." 
                           class="form-input w-64">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($units->count() > 0)
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-16">#</th>
                            <th>Nama Unit</th>
                            <th>Symbol</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Produk Menggunakan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="units-table-body">
                        @foreach($units as $index => $unit)
                        <tr class="hover:bg-gray-50 unit-row" data-search="{{ strtolower($unit->name . ' ' . $unit->symbol) }}">
                            <td class="font-medium text-gray-900">{{ $index + 1 }}</td>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="font-semibold text-green-600">{{ $unit->symbol }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $unit->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            Dibuat: {{ $unit->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    {{ $unit->symbol }}
                                </span>
                            </td>
                            <td>
                                <div class="max-w-xs">
                                    <p class="text-sm text-gray-600 truncate">
                                        {{ $unit->description ?: '-' }}
                                    </p>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($unit->product_units_count > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $unit->product_units_count }} produk
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Tidak digunakan
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($unit->product_units_count > 0)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-warning">Standby</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    
                                    <a href="{{ route('units.edit', $unit) }}" 
                                       class="text-green-600 hover:text-green-800 p-1" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($unit->product_units_count == 0)
                                    <form action="{{ route('units.destroy', $unit) }}" 
                                          method="POST" 
                                          class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 p-1" 
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-gray-400 p-1" title="Unit sedang digunakan">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-balance-scale text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada unit</h3>
                <p class="text-gray-500 mb-6">Mulai dengan menambahkan unit satuan pertama Anda</p>
                <a href="{{ route('units.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Tambah Unit Pertama
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('search');
    const tableRows = document.querySelectorAll('.unit-row');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        tableRows.forEach(row => {
            const searchData = row.getAttribute('data-search');
            if (searchData.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Delete confirmation
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            confirmDelete(() => {
                this.submit();
            });
        });
    });
});
</script>
@endsection