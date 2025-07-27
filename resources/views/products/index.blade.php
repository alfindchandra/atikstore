@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Produk</h1>
            <p class="text-gray-600">Kelola semua produk toko</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('categories.index') }}" class="btn-secondary">
                <i class="fas fa-tags mr-2"></i>
                Kelola Kategori
            </a>
            <a href="{{ route('products.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Tambah Produk
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <input 
                        type="text" 
                        id="searchInput"
                        class="form-input" 
                        placeholder="Cari produk atau barcode..."
                        onkeyup="searchProducts()"
                    >
                </div>
                <div>
                    <select id="categoryFilter" class="form-input" onchange="filterProducts()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select id="statusFilter" class="form-input" onchange="filterProducts()">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                        <option value="low_stock">Stok Menipis</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="productsGrid">
        @forelse($products as $product)
        <div class="card product-card" 
             data-name="{{ strtolower($product->name) }}" 
             data-barcode="{{ $product->barcode }}"
             data-category="{{ $product->category_id }}"
             data-status="{{ $product->is_active ? 'active' : 'inactive' }}{{ $product->isLowStock() ? ' low_stock' : '' }}">
            
            <div class="card-body">
                <!-- Product Header -->
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $product->name }}</h3>
                        @if($product->barcode)
                        <p class="text-xs text-gray-500 font-mono">{{ $product->barcode }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col items-end space-y-1">
                        <!-- Status Badge -->
                        <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        <!-- Low Stock Warning -->
                        @if($product->isLowStock())
                        <span class="badge badge-warning text-xs">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Stok Menipis
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Category -->
                <div class="mb-3">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-tag mr-1"></i>
                        {{ $product->category->name }}
                    </span>
                </div>

                <!-- Description -->
                @if($product->description)
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>
                @endif

                <!-- Stock Info -->
                <div class="mb-4">
                    <div class="text-sm text-gray-600 mb-2">
                        <i class="fas fa-warehouse mr-1"></i>
                        Stok Tersedia:
                    </div>
                    <div class="space-y-1">
                        @foreach($product->stocks as $stock)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $stock->unit->name }}:</span>
                            <span class="font-medium {{ $stock->quantity <= 0 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ number_format($stock->quantity, 0, ',', '.') }} {{ $stock->unit->symbol }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pricing -->
                <div class="mb-4">
                    <div class="text-sm text-gray-600 mb-2">
                        <i class="fas fa-money-bill-wave mr-1"></i>
                        Harga:
                    </div>
                    <div class="space-y-1">
                        @foreach($product->productUnits as $unit)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $unit->unit->name }}:</span>
                            <span class="font-medium text-green-600">
                                Rp {{ number_format($unit->price, 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                    <div class="flex space-x-2">
                        <a href="{{ route('products.show', $product) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm" 
                           title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('products.edit', $product) }}" 
                           class="text-green-600 hover:text-green-800 text-sm" 
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('stock.product', $product) }}" 
                           class="text-purple-600 hover:text-purple-800 text-sm" 
                           title="Kelola Stok">
                            <i class="fas fa-warehouse"></i>
                        </a>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Toggle Status -->
                        <button onclick="toggleProductStatus({{ $product->id }})" 
                                class="text-sm {{ $product->is_active ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800' }}" 
                                title="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <i class="fas {{ $product->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                        </button>
                        <!-- Delete -->
                        <button onclick="deleteProduct({{ $product->id }})" 
                                class="text-red-600 hover:text-red-800 text-sm" 
                                title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12">
                <i class="fas fa-box-open text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada produk</h3>
                <p class="text-gray-600 mb-6">Mulai dengan menambahkan produk pertama Anda</p>
                <a href="{{ route('products.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Produk
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="flex justify-center">
        {{ $products->links() }}
    </div>
    @endif

    <!-- Quick Stats -->
    @if($products->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Statistik Produk</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $products->total() }}</div>
                    <div class="text-sm text-gray-600">Total Produk</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">
                        {{ $products->where('is_active', true)->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Aktif</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-red-600 mb-2">
                        {{ $products->where('is_active', false)->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Nonaktif</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600 mb-2">
                        {{ $products->filter(function($p) { return $p->isLowStock(); })->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Stok Menipis</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function searchProducts() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const cards = document.querySelectorAll('.product-card');
    
    cards.forEach(card => {
        const name = card.getAttribute('data-name');
        const barcode = card.getAttribute('data-barcode');
        if (name.includes(filter) || (barcode && barcode.includes(filter))) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function filterProducts() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const cards = document.querySelectorAll('.product-card');
    
    cards.forEach(card => {
        let show = true;
        
        if (categoryFilter && card.getAttribute('data-category') !== categoryFilter) {
            show = false;
        }
        
        if (statusFilter) {
            const status = card.getAttribute('data-status');
            if (!status.includes(statusFilter)) {
                show = false;
            }
        }
        
        card.style.display = show ? '' : 'none';
    });
}

function toggleProductStatus(productId) {
    fetch(`/products/${productId}/toggle`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('error', 'Gagal mengubah status produk');
        }
    })
    .catch(error => {
        showAlert('error', 'Terjadi kesalahan');
    });
}

function deleteProduct(productId) {
    confirmDelete(() => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/products/${productId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }, 'Hapus produk ini?');
}

// Auto focus search input
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchInput').focus();
});

// Barcode scanner support
document.addEventListener('barcodeScan', function(e) {
    const barcode = e.detail.barcode;
    document.getElementById('searchInput').value = barcode;
    searchProducts();
});
</script>
@endsection