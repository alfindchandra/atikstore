@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Page Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Tambah Produk</h2>
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>
    </div>

    <form action="{{ route('products.store') }}" method="POST" id="productForm" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Dasar -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar</h3>
                    </div>
                    <div class="p-5 space-y-5">
                        <!-- Nama Produk -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-box mr-1 text-gray-400"></i> Nama Produk <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" 
                                   value="{{ old('name') }}"
                                   class="mt-2 block w-full py-2 px-2 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition @error('name') border-red-500 @enderror" 
                                   placeholder="Contoh: Indomie Goreng" required autofocus>
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Barcode -->
                        <div>
                            <label for="barcode" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-barcode mr-1 text-gray-400"></i> Barcode
                            </label>
                            <div class="mt-2 flex rounded-lg shadow-sm">
                                <input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}"
                                       class="flex-1 py-2 px-2 rounded-l-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition @error('barcode') border-red-500 @enderror" 
                                       placeholder="Scan atau ketik barcode">
                                <button type="button" 
                                        onclick="generateBarcode()"
                                        class="px-4 bg-gray-50 border border-l-0 border-gray-300 rounded-r-lg hover:bg-gray-100 transition"
                                        title="Generate Barcode">
                                    <i class="fas fa-random text-gray-500"></i>
                                </button>
                            </div>
                            @error('barcode')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-tags mr-1 text-gray-400"></i> Kategori <span class="text-red-500">*</span>
                            </label>
                             <div class="flex rounded-lg shadow-sm">
                                <select id="category_id" name="category_id"
                                    class="block w-full rounded-l-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @error('category_id') border-red-500 @enderror"
                                    required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('categories.create') }}" target="_blank"
                                    class="flex-shrink-0 inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 rounded-r-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors"
                                    title="Tambah Kategori Baru">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </a>
                            </div>
                            @error('category_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-align-left mr-1 text-gray-400"></i> Deskripsi
                            </label>
                            <textarea id="description" name="description" rows="3"
                                      class="mt-2 py-2 px-2 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition @error('description') border-red-500 @enderror"
                                      placeholder="Deskripsi produk (opsional)">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Minimum Stok -->
                        <div>
                            <label for="stock_alert_minimum" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-exclamation-triangle mr-1 text-gray-400"></i> Minimum Stok Alert <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="stock_alert_minimum" name="stock_alert_minimum" 
                                   value="{{ old('stock_alert_minimum', 10) }}"
                                   class="mt-2 block w-full py-2 px-2 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition @error('stock_alert_minimum') border-red-500 @enderror"
                                   min="0" step="1" required>
                            <p class="mt-1 text-xs text-gray-500">Sistem akan memberi peringatan jika stok di bawah nilai ini.</p>
                        </div>
                    </div>
                </div>

                <!-- Satuan & Harga -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900"><i class="fas fa-coins text-yellow-500 mr-2"></i> Satuan & Harga</h3>
                        <button type="button" onclick="addUnit()" 
                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-plus mr-1"></i> Tambah Satuan
                        </button>
                    </div>
                    <div class="p-5" id="unitsContainer">
                        <!-- Unit items will appear here -->
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Preview -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900"><i class="fas fa-eye mr-2"></i> Preview Produk</h3>
                    </div>
                    <div class="p-5 space-y-3 text-sm">
                        <h4 class="font-semibold text-gray-900" id="previewName">Nama Produk</h4>
                        <p class="text-gray-500" id="previewBarcode">Barcode akan muncul di sini</p>
                        <span class="inline-block bg-blue-100 text-blue-700 text-xs font-medium px-2 py-1 rounded-full" id="previewCategory">Kategori</span>
                        <p class="text-gray-600" id="previewDescription">Deskripsi produk</p>
                        <div class="pt-3 border-t border-gray-100">
                            <p class="text-gray-600 font-medium mb-2">Satuan & Harga:</p>
                            <div id="previewUnits" class="space-y-1 text-sm">
                                <p class="text-gray-400 italic">Belum ada satuan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900"><i class="fas fa-lightbulb text-yellow-500 mr-2"></i> Tips</h3>
                    </div>
                    <ul class="p-5 space-y-2 text-sm text-gray-600">
                        <li class="flex items-start"><i class="fas fa-check text-green-500 mr-2 mt-0.5"></i> Harus ada minimal satu satuan dasar.</li>
                        <li class="flex items-start"><i class="fas fa-check text-green-500 mr-2 mt-0.5"></i> Conversion rate menentukan konversi antar satuan.</li>
                        <li class="flex items-start"><i class="fas fa-check text-green-500 mr-2 mt-0.5"></i> Barcode harus unik jika diisi.</li>
                        <li class="flex items-start"><i class="fas fa-check text-green-500 mr-2 mt-0.5"></i> Satuan dasar memiliki conversion rate = 1.</li>
                        <li class="flex items-start"><i class="fas fa-check text-green-500 mr-2 mt-0.5"></i> Atur batas minimum dan maksimum pembelian.</li>
                        <li class="flex items-start"><i class="fas fa-check text-green-500 mr-2 mt-0.5"></i> Gunakan harga bertingkat untuk quantity discount.</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex flex-col space-y-3">
                        <button type="submit" id="submitBtn"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i> Simpan Produk
                        </button>
                        <a href="{{ route('products.index') }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Unit Template -->
<template id="unitTemplate">
    <div class="unit-item border border-gray-200 rounded-lg p-4 mb-4">
        <div class="flex justify-between items-start mb-4">
            <h4 class="font-medium text-gray-900">Satuan <span class="unit-number"></span></h4>
            <button type="button" onclick="removeUnit(this)" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        
        <!-- Informasi Dasar Satuan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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
                <label class="form-label">Harga Dasar <span class="text-red-500">*</span></label>
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

        <!-- Batas Pembelian -->
        <div class="border-t border-gray-100 pt-4 mb-4">
            <h5 class="font-medium text-gray-800 mb-3">
                <i class="fas fa-sliders-h text-purple-500 mr-2"></i> Batas Pembelian
            </h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Minimal Pembelian</label>
                    <input type="number" name="units[INDEX][min_purchase]" class="form-input" 
                           min="0" step="1" placeholder="1" value="1">
                    <p class="text-xs text-gray-500 mt-1">Jumlah minimal yang bisa dibeli</p>
                </div>
                
                <div>
                    <label class="form-label">Maksimal Pembelian</label>
                    <input type="number" name="units[INDEX][max_purchase]" class="form-input" 
                           min="0" step="1" placeholder="Kosongkan untuk unlimited">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan untuk unlimited</p>
                </div>
            </div>
        </div>

        <!-- Harga Bertingkat -->
        <div class="border-t border-gray-100 pt-4">
            <div class="flex items-center justify-between mb-3">
                <h5 class="font-medium text-gray-800">
                    <i class="fas fa-layer-group text-orange-500 mr-2"></i> Harga Bertingkat
                </h5>
                <label class="flex items-center">
                    <input type="checkbox" name="units[INDEX][enable_tiered_pricing]" value="1" 
                           class="tiered-pricing-checkbox mr-2" onchange="toggleTieredPricing(this)">
                    <span class="text-sm text-gray-700">Aktifkan Harga Bertingkat</span>
                </label>
            </div>
            
            <div class="tiered-pricing-container" style="display: none;">
                <div class="space-y-3">
                    <div class="text-sm text-gray-600 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                        <i class="fas fa-info-circle text-yellow-600 mr-1"></i>
                        Harga bertingkat memungkinkan Anda memberikan diskon berdasarkan jumlah pembelian.
                    </div>
                    
                    <div class="tiered-prices">
                       
                    </div>
                    
                    <button type="button" onclick="addTieredPrice(this)" 
                            class="inline-flex items-center px-3 py-1.5 text-sm bg-orange-50 text-orange-600 border border-orange-200 rounded-lg hover:bg-orange-100 transition">
                        <i class="fas fa-plus mr-1"></i> Tambah Tingkat Harga
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Tiered Price Template -->
<template id="tieredPriceTemplate">
    <div class="tiered-price-item bg-gray-50 p-3 rounded-lg border border-gray-200">
        <div class="flex justify-between items-start mb-2">
            <span class="text-sm font-medium text-gray-700">Tingkat <span class="tier-number"></span></span>
            <button type="button" onclick="removeTieredPrice(this)" class="text-red-600 hover:text-red-800 text-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-600">Jumlah Minimum</label>
                <input type="number" name="units[UNIT_INDEX][tiered_prices][TIER_INDEX][min_quantity]" 
                       class="form-input text-sm" min="1" step="1" required>
            </div>
            <div>
                <label class="text-xs text-gray-600">Harga</label>
                <input type="number" name="units[UNIT_INDEX][tiered_prices][TIER_INDEX][price]" 
                       class="form-input text-sm" min="0" step="1" required>
            </div>
        </div>
        
        <div class="mt-2">
            <input type="text" name="units[UNIT_INDEX][tiered_prices][TIER_INDEX][description]" 
                   class="form-input text-sm" placeholder="Deskripsi (opsional)">
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

function toggleTieredPricing(checkbox) {
    const unitItem = checkbox.closest('.unit-item');
    const container = unitItem.querySelector('.tiered-pricing-container');
    const tieredPricesDiv = unitItem.querySelector('.tiered-prices');
    
    if (checkbox.checked) {
        container.style.display = 'block';
        
        // Add first tiered price if none exists
        if (tieredPricesDiv.children.length === 0) {
            addTieredPrice(checkbox);
        }
    } else {
        container.style.display = 'none';
        // Clear all tiered prices
        tieredPricesDiv.innerHTML = '';
    }
    
    updatePreview();
}

function addTieredPrice(button) {
    const unitItem = button.closest('.unit-item');
    const tieredPricesDiv = unitItem.querySelector('.tiered-prices');
    const template = document.getElementById('tieredPriceTemplate');
    const clone = template.content.cloneNode(true);
    
    // Get unit index from the unit item
    const unitItems = Array.from(document.querySelectorAll('.unit-item'));
    const currentUnitIndex = unitItems.indexOf(unitItem);
    const tierIndex = tieredPricesDiv.children.length;
    
    // Replace placeholders
    let html = clone.querySelector('.tiered-price-item').outerHTML;
    html = html.replace(/UNIT_INDEX/g, currentUnitIndex);
    html = html.replace(/TIER_INDEX/g, tierIndex);
    
    tieredPricesDiv.insertAdjacentHTML('beforeend', html);
    
    // Update tier numbers
    updateTierNumbers(tieredPricesDiv);
    updatePreview();
}

function removeTieredPrice(button) {
    const tieredPriceItem = button.closest('.tiered-price-item');
    const tieredPricesDiv = tieredPriceItem.closest('.tiered-prices');
    
    tieredPriceItem.remove();
    updateTierNumbers(tieredPricesDiv);
    updatePreview();
}

function updateTierNumbers(tieredPricesDiv) {
    const items = tieredPricesDiv.querySelectorAll('.tiered-price-item');
    items.forEach((item, index) => {
        item.querySelector('.tier-number').textContent = index + 1;
    });
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
        const minPurchaseInput = item.querySelector('input[name*="[min_purchase]"]');
        const maxPurchaseInput = item.querySelector('input[name*="[max_purchase]"]');
        const isBase = item.querySelector('.unit-base-checkbox').checked;
        const hasTieredPricing = item.querySelector('.tiered-pricing-checkbox').checked;
        
        const unitText = unitSelect.options[unitSelect.selectedIndex]?.text || 'Pilih Satuan';
        const price = priceInput.value ? `Rp ${parseInt(priceInput.value).toLocaleString('id-ID')}` : 'Rp 0';
        const conversion = conversionInput.value || '1';
        const minPurchase = minPurchaseInput.value || '1';
        const maxPurchase = maxPurchaseInput.value || 'Unlimited';
        const baseText = isBase ? ' (Dasar)' : '';
        
        unitsHtml += `<div class="mb-3 p-2 bg-gray-50 rounded">
            <div class="flex justify-between items-start">
                <span class="font-medium">${unitText}${baseText}:</span>
                <span class="font-medium text-green-600">${price}</span>
            </div>`;
        
        // Purchase limits
        unitsHtml += `<div class="text-xs text-gray-500 mt-1">
            Min: ${minPurchase}, Max: ${maxPurchase}
        </div>`;
        
        // Conversion rate for non-base units
        if (!isBase && conversion !== '1') {
            unitsHtml += `<div class="text-xs text-gray-500">1 ${unitText} = ${conversion} satuan dasar</div>`;
        }
        
        // Tiered pricing
        if (hasTieredPricing) {
            const tieredPrices = item.querySelectorAll('.tiered-price-item');
            if (tieredPrices.length > 0) {
                unitsHtml += `<div class="text-xs text-orange-600 mt-1">
                    <i class="fas fa-layer-group mr-1"></i>Harga bertingkat (${tieredPrices.length} tingkat)
                </div>`;
                
                tieredPrices.forEach(tierItem => {
                    const minQty = tierItem.querySelector('input[name*="[min_quantity]"]').value || '0';
                    const tierPrice = tierItem.querySelector('input[name*="[price]"]').value || '0';
                    const tierPriceFormatted = parseInt(tierPrice) ? `Rp ${parseInt(tierPrice).toLocaleString('id-ID')}` : 'Rp 0';
                    
                    unitsHtml += `<div class="text-xs text-gray-500 ml-2">
                        ≥ ${minQty} unit: ${tierPriceFormatted}
                    </div>`;
                });
            }
        }
        
        unitsHtml += `</div>`;
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
        const minPurchaseInput = item.querySelector('input[name*="[min_purchase]"]');
        const maxPurchaseInput = item.querySelector('input[name*="[max_purchase]"]');
        
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
        
        // Validate purchase limits
        const minPurchase = parseFloat(minPurchaseInput.value) || 1;
        const maxPurchase = parseFloat(maxPurchaseInput.value) || null;
        
        if (minPurchase < 1) {
            isValid = false;
            errorMessage = `Satuan ${index + 1}: Minimal pembelian harus minimal 1`;
        }
        
        if (maxPurchase !== null && maxPurchase < minPurchase) {
            isValid = false;
            errorMessage = `Satuan ${index + 1}: Maksimal pembelian harus lebih besar atau sama dengan minimal pembelian`;
        }
        
        // Validate tiered pricing if enabled
        const hasTieredPricing = item.querySelector('.tiered-pricing-checkbox').checked;
        if (hasTieredPricing) {
            const tieredPrices = item.querySelectorAll('.tiered-price-item');
            const basePrice = parseFloat(priceInput.value) || 0;
            
            if (tieredPrices.length === 0) {
                isValid = false;
                errorMessage = `Satuan ${index + 1}: Tambahkan minimal satu tingkat harga`;
            }
            
            const quantities = [];
            tieredPrices.forEach((tierItem, tierIndex) => {
                const minQty = tierItem.querySelector('input[name*="[min_quantity]"]').value;
                const tierPrice = tierItem.querySelector('input[name*="[price]"]').value;
                
                if (!minQty || !tierPrice) {
                    isValid = false;
                    errorMessage = `Satuan ${index + 1} Tingkat ${tierIndex + 1}: Jumlah minimum dan harga harus diisi`;
                }
                
                const minQtyNum = parseFloat(minQty);
                const tierPriceNum = parseFloat(tierPrice);
                
                if (minQtyNum < 1) {
                    isValid = false;
                    errorMessage = `Satuan ${index + 1} Tingkat ${tierIndex + 1}: Jumlah minimum harus minimal 1`;
                }
                
                if (tierPriceNum < 0) {
                    isValid = false;
                    errorMessage = `Satuan ${index + 1} Tingkat ${tierIndex + 1}: Harga tidak boleh negatif`;
                }
                
                if (quantities.includes(minQtyNum)) {
                    isValid = false;
                    errorMessage = `Satuan ${index + 1}: Jumlah minimum tidak boleh sama`;
                }
                
                quantities.push(minQtyNum);
            });
        }
    });
    
    // Check for duplicate units
    const selectedUnits = [];
    unitItems.forEach((item, index) => {
        const unitValue = item.querySelector('.unit-select').value;
        if (unitValue) {
            if (selectedUnits.includes(unitValue)) {
                isValid = false;
                errorMessage = 'Tidak boleh ada satuan yang sama';
            }
            selectedUnits.push(unitValue);
        }
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
                
                // Purchase limits
                if (unitData.min_purchase) {
                    unitItem.querySelector('input[name*="[min_purchase]"]').value = unitData.min_purchase;
                }
                if (unitData.max_purchase) {
                    unitItem.querySelector('input[name*="[max_purchase]"]').value = unitData.max_purchase;
                }
                
                const checkbox = unitItem.querySelector('.unit-base-checkbox');
                if (unitData.is_base_unit) {
                    checkbox.checked = true;
                    handleBaseUnitChange(checkbox);
                }
                
                // Tiered pricing
                const tieredCheckbox = unitItem.querySelector('.tiered-pricing-checkbox');
                if (unitData.enable_tiered_pricing) {
                    tieredCheckbox.checked = true;
                    toggleTieredPricing(tieredCheckbox);
                    
                    if (unitData.tiered_prices) {
                        const tieredPricesDiv = unitItem.querySelector('.tiered-prices');
                        tieredPricesDiv.innerHTML = ''; // Clear default
                        
                        unitData.tiered_prices.forEach((tierData, tierIndex) => {
                            addTieredPrice(tieredCheckbox);
                            
                            const tierItems = tieredPricesDiv.querySelectorAll('.tiered-price-item');
                            const currentTier = tierItems[tierIndex];
                            
                            if (currentTier) {
                                currentTier.querySelector('input[name*="[min_quantity]"]').value = tierData.min_quantity || '';
                                currentTier.querySelector('input[name*="[price]"]').value = tierData.price || '';
                                currentTier.querySelector('input[name*="[description]"]').value = tierData.description || '';
                            }
                        });
                    }
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

// Add CSS for form styling
const style = document.createElement('style');
style.textContent = `
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.25rem;
    }
    
    .form-input {
        display: block;
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.15s ease-in-out;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .form-input:invalid {
        border-color: #ef4444;
    }
    
    .tiered-pricing-container {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
        }
        to {
            opacity: 1;
            max-height: 500px;
        }
    }
    
    .tiered-price-item {
        animation: fadeIn 0.2s ease-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection