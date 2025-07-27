@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-gray-800 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Kategori</h1>
        </div>
        <p class="text-gray-600">Buat kategori baru untuk mengelompokkan produk</p>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Informasi Kategori</h3>
        </div>
        
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            
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
                        value="{{ old('name') }}"
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
                    >{{ old('description') }}</textarea>
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
                                    Nama Kategori
                                </h3>
                                <p class="text-gray-600 text-sm" id="previewDescription">
                                    Deskripsi kategori akan muncul di sini
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-box mr-2"></i>
                                <span>0 produk</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="card-body border-t bg-gray-50">
                <div class="flex justify-between space-x-3">
                    <a href="{{ route('categories.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Kategori
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Help Card -->
    <div class="card mt-6">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                Tips Membuat Kategori
            </h3>
        </div>
        <div class="card-body">
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mr-2 mt-0.5 text-xs"></i>
                    Gunakan nama yang jelas dan mudah dipahami
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mr-2 mt-0.5 text-xs"></i>
                    Buat kategori yang tidak terlalu spesifik atau terlalu umum
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mr-2 mt-0.5 text-xs"></i>
                    Konsisten dengan penamaan yang sudah ada
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mr-2 mt-0.5 text-xs"></i>
                    Deskripsi membantu tim memahami tujuan kategori
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
        previewName.textContent = this.value || 'Nama Kategori';
    });

    descriptionInput.addEventListener('input', function() {
        previewDescription.textContent = this.value || 'Deskripsi kategori akan muncul di sini';
        previewDescription.style.display = this.value ? 'block' : 'block';
    });

    // Character counter for description
    const maxLength = 500;
    const counter = document.createElement('div');
    counter.className = 'text-sm text-gray-400 text-right mt-1';
    counter.textContent = `0/${maxLength}`;
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

    // Auto focus
    nameInput.focus();
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