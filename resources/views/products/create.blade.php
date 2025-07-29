@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-800 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Produk</h1>
        </div>
        <p class="text-gray-600">Buat produk baru dengan informasi lengkap</p>
    </div>

    <form action="{{ route('products.store') }}" method="POST" id="productForm">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar</h3>
                    </div>
                    <div class="card-body space-y-4">
                        <!-- Product Name -->
                        <div>
                            <label for="name" class="form-label">
                                <i class="fas fa-box text-gray-400 mr-2"></i>
                                Nama Produk <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}"
                                class="form-input @error('name') border-red-500 @enderror" 
                                placeholder="Contoh: Indomie Goreng"
                                required
                                autofocus
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Barcode -->
                        <div>
                            <label for="barcode" class="form-label">
                                <i class="fas fa-barcode text-gray-400 mr-2"></i>
                                Barcode
                            </label>
                            <div class="flex">
                                <input 
                                    type="text" 
                                    id="barcode" 
                                    name="barcode" 
                                    value="{{ old('barcode') }}"
                                    class="form-input rounded-r-none @error('barcode') border-red-500 @enderror" 
                                    placeholder="Scan atau ketik barcode"
                                >
                                <button 
                                    type="button" 
                                    class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200"
                                    onclick="generateBarcode()"
                                    title="Generate Barcode"
                                >
                                    <i class="fas fa-random text-gray-600"></i>
                                </button>
                            </div>
                            @error('barcode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="form-label">
                                <i class="fas fa-tags text-gray-400 mr-2"></i>
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <div class="flex">
                                <select 
                                    id="category_id" 
                                    name="category_id" 
                                    class="form-input rounded-r-none @error('category_id') border-red-500 @enderror"
                                    required
                                >
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <a 
                                    href="{{ route('categories.create') }}" 
                                    class="px-3 py-2 bg-blue-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-blue-200 text-blue-600"
                                    title="Tambah Kategori Baru"
                                    target="_blank"
                                >
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left text-gray-400 mr-2"></i>
                                Deskripsi
                            </label>
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="3"
                                class="form-input @error('description') border-red-500 @enderror" 
                                placeholder="Deskripsi produk (opsional)"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock Alert Minimum -->
                        <div>
                            <label for="stock_alert_minimum" class="form-label">
                                <i class="fas fa-exclamation-triangle text-gray-400 mr-2"></i>
                                Minimum Stok Alert <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="stock_alert_minimum" 
                                name="stock_alert_minimum" 
                                value="{{ old('stock_alert_minimum', '10') }}"
                                class="form-input @error('stock_alert_minimum') border-red-500 @enderror" 
                                min="0"
                                step="0.01"
                                required
                            >
                            @error('stock_alert_minimum')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Sistem akan memberi peringatan jika stok di bawah nilai ini
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Units & Pricing -->
                <div class="card">
                    <div class="card-header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Satuan & Harga</h3>
                            <button type="button" onclick="addUnit()" class="btn-primary text-sm">
                                <i class="fas fa-plus mr-1"></i>
                                Tambah Satuan
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="unitsContainer">
                            <!-- Unit items will be added here -->
                        </div>
                        @error('units')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        {{-- Show specific unit field errors --}}
                        @if($errors->has('units.*'))
                            @foreach($errors->get('units.*') as $key => $messages)
                                @foreach($messages as $message)
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @endforeach
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Preview -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-eye mr-2"></i>
                            Preview Produk
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-3">
                            <div>
                                <h4 class="font-semibold text-gray-900" id="previewName">Nama Produk</h4>
                                <p class="text-sm text-gray-500" id="previewBarcode">Barcode akan muncul di sini</p>
                            </div>
                            <div>
                                <span class="badge badge-primary" id="previewCategory">Kategori</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600" id="previewDescription">Deskripsi produk</p>
                            </div>
                            <div class="pt-3 border-t">
                                <div class="text-sm text-gray-600 mb-2">Satuan & Harga:</div>
                                <div id="previewUnits" class="space-y-1 text-sm">
                                    <div class="text-gray-500 italic">Belum ada satuan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                            Tips
                        </h3>
                    </div>
                    <div class="card-body">
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-0.5 text-xs"></i>
                                Harus ada minimal satu satuan dasar
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-0.5 text-xs"></i>
                                Conversion rate menentukan konversi antar satuan
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-0.5 text-xs"></i>
                                Barcode harus unik jika diisi
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-0.5 text-xs"></i>
                                Satuan dasar memiliki conversion rate = 1
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="flex flex-col space-y-3">
                            <button type="submit" class="btn-primary w-full" id="submitBtn">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Produk
                            </button>
                            <a href="{{ route('products.index') }}" class="btn-secondary w-full text-center">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Unit Template -->
<template id="unitTemplate">
    <div class="unit-item border border-gray-200 rounded-lg p-4 mb-4">
        <div class="flex justify-between items-start mb-3">
            <h4 class="font-medium text-gray-900">Satuan <span class="unit-number"></span></h4>
            <button type="button" onclick="removeUnit(this)" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                <select name="units[INDEX][unit_id]" class="form-input unit-select" required>
                    <option value="">Pilih Satuan</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->symbol }})</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="form-label">Harga <span class="text-red-500">*</span></label>
                <input type="number" name="units[INDEX][price]" class="form-input unit-price" 
                       min="0" step="1" placeholder="0" required>
            </div>
            
            <div>
                <label class="form-label">Conversion Rate <span class="text-red-500">*</span></label>
                <input type="number" name="units[INDEX][conversion_rate]" class="form-input unit-conversion" 
                       min="0.0001" step="0.0001" placeholder="1" required>
                <p class="text-xs text-gray-500 mt-1">1 satuan ini = ? satuan dasar</p>
            </div>
            
            <div class="flex items-center">
                <label class="flex items-center">
                    <input type="checkbox" name="units[INDEX][is_base_unit]" value="1" class="unit-base-checkbox mr-2" 
                           onchange="handleBaseUnitChange(this)">
                    <span class="text-sm text-gray-700">Satuan Dasar</span>
                </label>
            </div>
        </div>
    </div>
</template>

<script>
let unitIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Add first unit by default
    addUnit();
    
    // Setup preview updates
    setupPreviewUpdates();
    
    // Focus on name input
    document.getElementById('name').focus();
    
    // Load old values if exists (validation failed)
    loadOldValues();
});

function addUnit() {
    const container = document.getElementById('unitsContainer');
    const template = document.getElementById('unitTemplate');
    const clone = template.content.cloneNode(true);
    
    // Replace INDEX with actual index
    const html = clone.querySelector('.unit-item').outerHTML.replace(/INDEX/g, unitIndex);
    container.insertAdjacentHTML('beforeend', html);
    
    // Update unit number
    const unitItems = container.querySelectorAll('.unit-item');
    unitItems[unitItems.length - 1].querySelector('.unit-number').textContent = unitItems.length;
    
    unitIndex++;
    updatePreview();
}

function removeUnit(button) {
    const unitItem = button.closest('.unit-item');
    const container = document.getElementById('unitsContainer');
    
    // Don't allow removing if it's the only unit
    if (container.querySelectorAll('.unit-item').length <= 1) {
        showAlert('warning', 'Harus ada minimal satu satuan');
        return;
    }
    
    unitItem.remove();
    
    // Update unit numbers
    const unitItems = container.querySelectorAll('.unit-item');
    unitItems.forEach((item, index) => {
        item.querySelector('.unit-number').textContent = index + 1;
    });
    
    updatePreview();
}

function handleBaseUnitChange(checkbox) {
    if (checkbox.checked) {
        // Uncheck other base unit checkboxes
        const otherCheckboxes = document.querySelectorAll('.unit-base-checkbox');
        otherCheckboxes.forEach(cb => {
            if (cb !== checkbox) {
                cb.checked = false;
            }
        });
        
        // Set conversion rate to 1 for base unit
        const unitItem = checkbox.closest('.unit-item');
        const conversionInput = unitItem.querySelector('.unit-conversion');
        conversionInput.value = '1';
        conversionInput.readOnly = true;
        conversionInput.style.backgroundColor = '#f3f4f6';
    } else {
        // Make conversion rate editable again
        const unitItem = checkbox.closest('.unit-item');
        const conversionInput = unitItem.querySelector('.unit-conversion');
        conversionInput.readOnly = false;
        conversionInput.style.backgroundColor = '';
    }
    
    updatePreview();
}

function generateBarcode() {
    const timestamp = Date.now().toString();
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    const barcode = timestamp.slice(-8) + random;
    document.getElementById('barcode').value = barcode;
    updatePreview();
}

function setupPreviewUpdates() {
    // Name
    document.getElementById('name').addEventListener('input', updatePreview);
    
    // Barcode
    document.getElementById('barcode').addEventListener('input', updatePreview);
    
    // Category
    document.getElementById('category_id').addEventListener('change', updatePreview);
    
    // Description
    document.getElementById('description').addEventListener('input', updatePreview);
    
    // Units - use event delegation
    document.getElementById('unitsContainer').addEventListener('input', updatePreview);
    document.getElementById('unitsContainer').addEventListener('change', updatePreview);
}

function updatePreview() {
    // Update name
    const name = document.getElementById('name').value || 'Nama Produk';
    document.getElementById('previewName').textContent = name;
    
    // Update barcode
    const barcode = document.getElementById('barcode').value;
    document.getElementById('previewBarcode').textContent = barcode || 'Barcode akan muncul di sini';
    
    // Update category
    const categorySelect = document.getElementById('category_id');
    const categoryText = categorySelect.options[categorySelect.selectedIndex]?.text || 'Kategori';
    document.getElementById('previewCategory').textContent = categoryText;
    
    // Update description
    const description = document.getElementById('description').value || 'Deskripsi produk';
    document.getElementById('previewDescription').textContent = description;
    
    // Update units
    const unitsContainer = document.getElementById('unitsContainer');
    const unitItems = unitsContainer.querySelectorAll('.unit-item');
    const previewUnits = document.getElementById('previewUnits');
    
    if (unitItems.length === 0) {
        previewUnits.innerHTML = '<div class="text-gray-500 italic">Belum ada satuan</div>';
        return;
    }
    
    let unitsHtml = ''; 
    unitItems.forEach(item => {
        const unitSelect = item.querySelector('.unit-select');
        const priceInput = item.querySelector('.unit-price');
        const conversionInput = item.querySelector('.unit-conversion');
        const isBase = item.querySelector('.unit-base-checkbox').checked;
        
        const unitText = unitSelect.options[unitSelect.selectedIndex]?.text || 'Pilih Satuan';
        const price = priceInput.value ? `Rp ${parseInt(priceInput.value).toLocaleString('id-ID')}` : 'Rp 0';
        const conversion = conversionInput.value || '1';
        const baseText = isBase ? ' (Dasar)' : '';
        
        unitsHtml += `<div class="flex justify-between">
            <span>${unitText}${baseText}:</span>
            <span class="font-medium text-green-600">${price}</span>
        </div>`;
        
        if (!isBase && conversion !== '1') {
            unitsHtml += `<div class="text-xs text-gray-500 ml-2">1 ${unitText} = ${conversion} satuan dasar</div>`;
        }
    });
    
    previewUnits.innerHTML = unitsHtml;
}

// Form validation
document.getElementById('productForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    
    // Disable submit button to prevent double submission
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    
    // Check if at least one base unit is selected
    const baseUnits = document.querySelectorAll('.unit-base-checkbox:checked');
    if (baseUnits.length === 0) {
        e.preventDefault();
        showAlert('error', 'Harus ada satu satuan yang dijadikan sebagai satuan dasar');
        resetSubmitButton();
        return false;
    }
    
    if (baseUnits.length > 1) {
        e.preventDefault();
        showAlert('error', 'Hanya boleh ada satu satuan dasar');
        resetSubmitButton();
        return false;
    }
    
    // Check if all required fields are filled
    const unitItems = document.querySelectorAll('.unit-item');
    let isValid = true;
    let errorMessage = '';
    
    unitItems.forEach((item, index) => {
        const unitSelect = item.querySelector('.unit-select');
        const priceInput = item.querySelector('.unit-price');
        const conversionInput = item.querySelector('.unit-conversion');
        
        if (!unitSelect.value) {
            isValid = false;
            errorMessage = `Satuan ${index + 1}: Pilih satuan`;
        } else if (!priceInput.value || parseFloat(priceInput.value) < 0) {
            isValid = false;
            errorMessage = `Satuan ${index + 1}: Harga harus diisi dan tidak boleh negatif`;
        } else if (!conversionInput.value || parseFloat(conversionInput.value) < 0.0001) {
            isValid = false;
            errorMessage = `Satuan ${index + 1}: Conversion rate harus diisi dan minimal 0.0001`;
        }
    });
    
    // Check for duplicate units
    const selectedUnits = [];
    unitItems.forEach(item => {
        const unitId = item.querySelector('.unit-select').value;
        if (unitId && selectedUnits.includes(unitId)) {
            isValid = false;
            errorMessage = 'Tidak boleh ada satuan yang sama';
        }
        if (unitId) selectedUnits.push(unitId);
    });
    
    if (!isValid) {
        e.preventDefault();
        showAlert('error', errorMessage);
        resetSubmitButton();
        return false;
    }
});

function resetSubmitButton() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Simpan Produk';
}

function showAlert(type, message) {
    // You can implement your alert system here
    // For now, using simple alert
    alert(message);
}

// Load old values when validation fails
function loadOldValues() {
    @if(old('units'))
        const oldUnits = @json(old('units'));
        
        // Clear existing units
        document.getElementById('unitsContainer').innerHTML = '';
        unitIndex = 0;
        
        // Add units from old input
        oldUnits.forEach((unitData, index) => {
            addUnit();
            
            const unitItem = document.querySelectorAll('.unit-item')[index];
            if (unitItem) {
                unitItem.querySelector('.unit-select').value = unitData.unit_id || '';
                unitItem.querySelector('.unit-price').value = unitData.price || '';
                unitItem.querySelector('.unit-conversion').value = unitData.conversion_rate || '';
                
                const checkbox = unitItem.querySelector('.unit-base-checkbox');
                if (unitData.is_base_unit) {
                    checkbox.checked = true;
                    handleBaseUnitChange(checkbox);
                }
            }
        });
        
        updatePreview();
    @endif
}

// Barcode scanner support
document.addEventListener('barcodeScan', function(e) {
    const barcode = e.detail.barcode;
    document.getElementById('barcode').value = barcode;
    updatePreview();
});
</script>
@endsection