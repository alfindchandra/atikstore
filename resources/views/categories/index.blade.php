@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kategori Produk</h1>
            <p class="text-gray-600">Kelola kategori produk toko</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>
            Tambah Kategori
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        id="searchInput"
                        class="form-input" 
                        placeholder="Cari kategori..."
                        onkeyup="searchCategories()"
                    >
                </div>
                <div class="flex gap-2">
                    <button class="btn-secondary" onclick="resetFilters()">
                        <i class="fas fa-undo mr-1"></i>
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="categoriesGrid">
        @forelse($categories as $category)
        <div class="card category-card" data-name="{{ strtolower($category->name) }}">
            <div class="card-body">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $category->name }}</h3>
                        @if($category->description)
                        <p class="text-gray-600 text-sm mb-3">{{ $category->description }}</p>
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('categories.edit', $category) }}" 
                           class="text-blue-600 hover:text-blue-800 p-1" 
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteCategory({{ $category->id }})" 
                                class="text-red-600 hover:text-red-800 p-1" 
                                title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-box mr-2"></i>
                        <span>{{ $category->products_count }} produk</span>
                    </div>
                    <a href="{{ route('products.index', ['category' => $category->id]) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Lihat Produk →
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12">
                <i class="fas fa-tags text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada kategori</h3>
                <p class="text-gray-600 mb-6">Mulai dengan menambahkan kategori pertama Anda</p>
                <a href="{{ route('categories.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Kategori
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Quick Stats -->
    @if($categories->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Statistik Kategori</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $categories->count() }}</div>
                    <div class="text-sm text-gray-600">Total Kategori</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ $categories->sum('products_count') }}</div>
                    <div class="text-sm text-gray-600">Total Produk</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2">
                        {{ $categories->where('products_count', '>', 0)->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Kategori Aktif</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function searchCategories() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const cards = document.querySelectorAll('.category-card');
    
    cards.forEach(card => {
        const name = card.getAttribute('data-name');
        if (name.includes(filter)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    searchCategories();
}

function deleteCategory(categoryId) {
    confirmDelete(() => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/categories/${categoryId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }, 'Hapus kategori ini?');
}

// Auto focus search input
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchInput').focus();
});
</script>
@endsection