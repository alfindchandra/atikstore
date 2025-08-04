@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Unit Baru</h1>
            <p class="text-gray-600">Tambahkan satuan unit untuk produk Anda</p>
        </div>
        <a href="{{ route('units.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="max-w-2xl">
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Unit</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('units.store') }}" method="POST" id="unit-form">
                    @csrf
                    
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
                                   value="{{ old('name') }}" 
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
                                   value="{{ old('symbol') }}" 
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
                                      placeholder="Deskripsi tambahan tentang unit ini...">{{ old('description') }}</textarea>
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
                                    <span class="font-semibold text-green-600" id="symbol-preview">-</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900" id="name-preview">Nama Unit</div>
                                    <div class="text-sm text-gray-500" id="description-preview">Deskripsi unit</div>
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
                            <i class="fas fa-save mr-2"></i>Simpan Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Common Units Examples -->
    <div class="max-w-2xl">
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Contoh Unit Umum</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Unit Berat</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kilogram</span>
                                <span class="text-gray-500">kg</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Gram</span>
                                <span class="text-gray-500">g</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ton</span>
                                <span class="text-gray-500">ton</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Unit Volume</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Liter</span>
                                <span class="text-gray-500">l</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Mililiter</span>
                                <span class="text-gray-500">ml</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Galon</span>
                                <span class="text-gray-500">gal</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Unit Panjang</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Meter</span>
                                <span class="text-gray-500">m</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Centimeter</span>
                                <span class="text-gray-500">cm</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kilometer</span>
                                <span class="text-gray-500">km</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Unit Lainnya</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pieces</span>
                                <span class="text-gray-500">pcs</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Dozen</span>
                                <span class="text-gray-500">dzn</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Box</span>
                                <span class="text-gray-500">box</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-lightbulb text-blue-600 mt-0.5 mr-2"></i>
                        <div class="text-sm text-blue-800">
                            <strong>Tips:</strong> Pilih unit yang sesuai dengan jenis produk Anda. 
                            Gunakan symbol yang mudah diingat dan umum digunakan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    });

    // Auto-generate symbol suggestion from name
    nameInput.addEventListener('blur', function() {
        const name = this.value.toLowerCase();
        if (!symbolInput.value && name) {
            let suggestion = '';
            
            // Common unit suggestions
            const suggestions = {
                'kilogram': 'kg',
                'gram': 'g',
                'ton': 'ton',
                'liter': 'l',
                'mililiter': 'ml',
                'meter': 'm',
                'centimeter': 'cm',
                'kilometer': 'km',
                'pieces': 'pcs',
                'dozen': 'dzn',
                'box': 'box',
                'pack': 'pack',
                'bottle': 'btl'
            };

            if (suggestions[name]) {
                suggestion = suggestions[name];
            } else {
                // Generate from first few characters
                suggestion = name.substring(0, 3);
            }

            symbolInput.value = suggestion;
            symbolPreview.textContent = suggestion;
        }
    });
});
</script>
@endsection