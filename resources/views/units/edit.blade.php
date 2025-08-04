@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Unit</h1>
            <p class="text-gray-600">Perbarui informasi unit {{ $unit->name }}</p>
        </div>
        <div class="flex space-x-3">
            
            <a href="{{ route('units.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Warning if unit is in use -->
    @if($unit->productUnits()->count() > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
            <div>
                <h4 class="font-medium text-yellow-800">Perhatian!</h4>
                <p class="text-yellow-700 text-sm">
                    Unit ini sedang digunakan oleh {{ $unit->productUnits()->count() }} produk. 
                    Perubahan nama dan symbol akan mempengaruhi tampilan produk tersebut.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="max-w-2xl">
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Unit</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('units.update', $unit) }}" method="POST" id="unit-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Unit Name -->
                        <div>
                            <label for="name" class="form-label">
                                Nama Unit <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-input @error('name') border-red-500 @enderror" 
                                   value="{{ old('name', $unit->name) }}" 
                                   placeholder="Contoh: Kilogram, Meter, Pieces"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">Masukkan nama lengkap unit satuan</p>
                        </div>

                        <!-- Unit Symbol -->
                        <div>
                            <label for="symbol" class="form-label">
                                Symbol Unit <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="symbol" 
                                   name="symbol" 
                                   class="form-input @error('symbol') border-red-500 @enderror" 
                                   value="{{ old('symbol', $unit->symbol) }}" 
                                   placeholder="Contoh: kg, m, pcs"
                                   maxlength="10"
                                   required>
                            @error('symbol')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">Singkatan unit (maksimal 10 karakter)</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="form-label">
                                Deskripsi <span class="text-gray-500">(Opsional)</span>
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3" 
                                      class="form-input @error('description') border-red-500 @enderror"
                                      placeholder="Deskripsi tambahan tentang unit ini...">{{ old('description', $unit->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">Informasi tambahan tentang penggunaan unit ini</p>
                        </div>

                        <!-- Preview Card -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Preview Unit</h4>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                    <span class="font-semibold text-green-600" id="symbol-preview">{{ $unit->symbol }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900" id="name-preview">{{ $unit->name }}</div>
                                    <div class="text-sm text-gray-500" id="description-preview">{{ $unit->description ?: 'Deskripsi unit' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                        <a href="{{ route('units.index') }}" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                        <button type="submit" class="btn-primary" id="submit-btn">
                            <i class="fas fa-save mr-2"></i>Perbarui Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Usage Information -->
    @if($unit->productUnits()->count() > 0)
    <div class="max-w-2xl">
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Produk Menggunakan Unit Ini</h3>
            </div>
            <div class="card-body">
                <div class="space-y-3">
                    @foreach($unit->productUnits()->with('product')->limit(5)->get() as $productUnit)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-box text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $productUnit->product->name }}</div>
                                    <div class="text-sm text-gray-500">
                                        Harga: {{ number_format($productUnit->price, 0, ',', '.') }} per {{ $unit->symbol }}
                                    </div>
                                </div>
                            </div>
                            @if($productUnit->is_base_unit)
                                <span class="badge badge-success">Unit Dasar</span>
                            @else
                                <span class="badge badge-info">Unit Tambahan</span>
                            @endif
                        </div>
                    @endforeach

                    @if($unit->productUnits()->count() > 5)
                        <div class="text-center py-2">
                            <span class="text-sm text-gray-500">
                                Dan {{ $unit->productUnits()->count() - 5 }} produk lainnya...
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const symbolInput = document.getElementById('symbol');
    const descriptionInput = document.getElementById('description');
    
    const namePreview = document.getElementById('name-preview');
    const symbolPreview = document.getElementById('symbol-preview');
    const descriptionPreview = document.getElementById('description-preview');

    // Real-time preview updates
    nameInput.addEventListener('input', function() {
        namePreview.textContent = this.value || 'Nama Unit';
    });

    symbolInput.addEventListener('input', function() {
        symbolPreview.textContent = this.value || '-';
    });

    descriptionInput.addEventListener('input', function() {
        descriptionPreview.textContent = this.value || 'Deskripsi unit';
    });

    // Form validation
    const form = document.getElementById('unit-form');
    const submitBtn = document.getElementById('submit-btn');

    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memperbarui...';
    });
});
</script>
@endsection