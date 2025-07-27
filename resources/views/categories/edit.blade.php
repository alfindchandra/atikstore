@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-gray-800 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Kategori</h1>
        </div>
        <p class="text-gray-600">Perbarui informasi kategori "{{ $category->name }}"</p>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Kategori</h3>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-box mr-1"></i>
                    <span>{{ $category->products_count }} produk</span>
                </div>
            </div>
        </div>
        
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="card-body space-y-6">
                <!-- Name Field -->
                <div>
                    <label for="name" class="form-label">
                        <i class="fas fa-tag text-gray-400 mr-2"></i>
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $category->name) }}"
                        class="form-input @error('name') border-red-500 @enderror" 
                        placeholder="Contoh: Makanan Ringan"
                        required
                        autofocus
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Nama kategori harus unik dan deskriptif
                    </p>
                </div>

                <!-- Description Field -->
                <div>
                    <label for="description" class="form-label">
                        <i class="fas fa-align-left text-gray-400 mr-2"></i>
                        Deskripsi
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4"
                        class="form-input @error('description') border-red-500 @enderror" 
                        placeholder="Deskripsi kategori (opsional)"
                    >{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Maksimal 500 karakter
                    </p>
                </div>

                <!-- Preview Card -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">
                        <i class="fas fa-eye mr-2"></i>
                        Preview Kategori
                    </h4>
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2" id="previewName">
                                    {{ $category->name }}
                                </h3>
                                <p class="text-gray-600 text-sm" id="previewDescription">
                                    {{ $category->description ?: 'Deskripsi kategori akan muncul di sini' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-box mr-2"></i>
                                <span>{{ $category->products_count }} produk</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warning if category has products -->
                @if($category->products_count > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mr-3 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800 mb-1">Perhatian</h4>
                            <p class="text-sm text-yellow-700">
                                Kategori ini memiliki {{ $category->products_count }} produk. 
                                Perubahan nama akan mempengaruhi semua produk dalam kategori ini.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Form Actions -->
            <div class="card-body border-t bg-gray-50">
                <div class="flex justify-between space-x-3">
                    <a href="{{ route('categories.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <div class="flex space-x-3">
                        @if($category->products_count > 0)
                        <a href="{{ route('products.index', ['category' => $category->id]) }}" 
                           class="btn-secondary">
                            <i class="fas fa-box mr-2"></i>
                            Lihat Produk
                        </a>
                        @endif
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Perbarui Kategori
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Category Stats -->
    @if($category->products_count > 0)
    <div class="card mt-6">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                Statistik Kategori
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $category->products_count }}</div>
                    <div class="text-sm text-gray-600">Total Produk</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">
                        {{ $category->products()->where('is_active', true)->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Produk Aktif</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-600 mb-2">
                        {{ $category->products()->where('is_active', false)->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Produk Nonaktif</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Help Card -->
    <div class="card mt-6">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                Tips Edit Kategori
            </h3>
        </div>
        <div class="card-body">
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mr-2 mt-0.5 text-xs"></i>
                    Perubahan nama kategori akan berlaku untuk semua produk
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mr-2 mt-0.5 text-xs"></i>
                    Pastikan nama tetap konsisten dengan kategori lain
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mr-2 mt-0.5 text-xs"></i>
                    Deskripsi yang jelas membantu pengelolaan produk
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const previewName = document.getElementById('previewName');
    const previewDescription = document.getElementById('previewDescription');

    // Update preview on input
    nameInput.addEventListener('input', function() {
        previewName.textContent = this.value || '{{ $category->name }}';
    });

    descriptionInput.addEventListener('input', function() {
        previewDescription.textContent = this.value || 'Deskripsi kategori akan muncul di sini';
    });

    // Character counter for description
    const maxLength = 500;
    const counter = document.createElement('div');
    counter.className = 'text-sm text-gray-400 text-right mt-1';
    const currentLength = descriptionInput.value.length;
    counter.textContent = `${currentLength}/${maxLength}`;
    descriptionInput.parentNode.appendChild(counter);

    descriptionInput.addEventListener('input', function() {
        const remaining = maxLength - this.value.length;
        counter.textContent = `${this.value.length}/${maxLength}`;
        
        if (remaining < 50) {
            counter.className = 'text-sm text-orange-500 text-right mt-1';
        } else if (remaining < 20) {
            counter.className = 'text-sm text-red-500 text-right mt-1';
        } else {
            counter.className = 'text-sm text-gray-400 text-right mt-1';
        }
    });
});

// Form validation
function validateForm() {
    const name = document.getElementById('name').value.trim();
    
    if (!name) {
        showAlert('error', 'Nama kategori wajib diisi');
        return false;
    }
    
    if (name.length < 2) {
        showAlert('error', 'Nama kategori minimal 2 karakter');
        return false;
    }
    
    return true;
}

// Add form submit validation
document.querySelector('form').addEventListener('submit', function(e) {
    if (!validateForm()) {
        e.preventDefault();
    }
});
</script>
@endsection