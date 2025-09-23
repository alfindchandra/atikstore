@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center mb-8">
        <a href="{{ route('purchases.show', $purchase) }}" 
           class="mr-4 text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Pembelian</h1>
            <p class="text-gray-600">{{ $purchase->purchase_number }}</p>
        </div>
    </div>

    <form action="{{ route('purchases.update', $purchase) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')
        
        <!-- Purchase Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-info-circle mr-3 text-blue-600"></i>
                Informasi Pembelian
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Supplier <span class="text-red-500">*</span>
                    </label>
                    <select id="supplier_id" 
                            name="supplier_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('supplier_id') border-red-500 @enderror">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" 
                                {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Pembelian <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           id="purchase_date" 
                           name="purchase_date" 
                           value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('purchase_date') border-red-500 @enderror">
                    @error('purchase_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="receipt_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Struk Pembelian
                        @if($purchase->receipt_image)
                            <span class="text-green-600 text-sm">(Sudah ada gambar)</span>
                        @endif
                    </label>
                    
                    @if($purchase->receipt_image)
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Struk saat ini:</p>
                        <div class="relative inline-block">
                            <img src="{{ $purchase->receipt_image_url }}" 
                                 alt="Current Receipt" 
                                 class="max-w-xs max-h-32 rounded-lg shadow-sm cursor-pointer"
                                 onclick="showReceiptImage('{{ $purchase->receipt_image_url }}')">
                            <button type="button" 
                                    onclick="showReceiptImage('{{ $purchase->receipt_image_url }}')"
                                    class="absolute top-2 right-2 bg-black bg-opacity-50 text-white rounded-full p-1 hover:bg-opacity-70">
                                <i class="fas fa-expand-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <div id="image-preview" class="hidden">
                                <img id="preview-img" src="" alt="Preview" class="max-w-xs max-h-48 mx-auto rounded-lg shadow-sm">
                            </div>
                            <div id="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                <div class="text-sm text-gray-600">
                                    <label for="receipt_image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>{{ $purchase->receipt_image ? 'Ganti foto struk' : 'Upload foto struk' }}</span>
                                        <input id="receipt_image" name="receipt_image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 5MB</p>
                            </div>
                        </div>
                    </div>
                    @error('receipt_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 resize-none"
                              placeholder="Catatan tambahan untuk pembelian ini">{{ old('notes', $purchase->notes) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Items Selection -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-box mr-3 text-green-600"></i>
                Item Pembelian
            </h2>
            
            <div class="mb-6">
                <button type="button" 
                        onclick="addItem()" 
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Item</span>
                </button>
            </div>

            <div id="items-container" class="space-y-4">
                <!-- Existing items will be populated here -->
            </div>

            @error('items')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-calculator mr-3 text-purple-600"></i>
                Ringkasan
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="tax_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Pajak/Biaya Tambahan
                    </label>
                    <input type="number" 
                           id="tax_amount" 
                           name="tax_amount" 
                           value="{{ old('tax_amount', $purchase->tax_amount) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           onchange="updateTotal()">
                </div>

                <div>
                    <label for="discount_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Diskon
                    </label>
                    <input type="number" 
                           id="discount_amount" 
                           name="discount_amount" 
                           value="{{ old('discount_amount', $purchase->discount_amount) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           onchange="updateTotal()">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Total Pembayaran
                    </label>
                    <div id="total-display" class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-lg font-bold text-gray-900">
                        Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('purchases.show', $purchase) }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center space-x-2">
                <i class="fas fa-times"></i>
                <span>Batal</span>
            </a>
            
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center space-x-2">
                <i class="fas fa-save"></i>
                <span>Update Pembelian</span>
            </button>
        </div>
    </form>
</div>

<!-- Modal for Receipt Image -->
<div id="receiptModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Struk Saat Ini</h3>
                    <button onclick="closeReceiptModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <img id="receiptImage" src="" alt="Struk Pembelian" class="w-full h-auto rounded-lg shadow-sm">
            </div>
        </div>
    </div>
</div>

<script>
let itemCounter = 0;
const products = @json($products);
const existingDetails = @json($purchase->details);

function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const placeholder = document.getElementById('upload-placeholder');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function showReceiptImage(imageUrl) {
    document.getElementById('receiptImage').src = imageUrl;
    document.getElementById('receiptModal').classList.remove('hidden');
}

function closeReceiptModal() {
    document.getElementById('receiptModal').classList.add('hidden');
}

function addItem(productId = '', unitId = '', quantity = '', unitCost = '') {
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="item-row border border-gray-200 rounded-lg p-4 bg-gray-50" data-index="${itemCounter}">
            <div class="flex justify-between items-start mb-4">
                <h4 class="font-medium text-gray-900">Item #${itemCounter + 1}</h4>
                <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-900">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Produk</label>
                    <select name="items[${itemCounter}][product_id]" onchange="updateUnits(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih Produk</option>
                        ${products.map(product => 
                            `<option value="${product.id}" ${product.id == productId ? 'selected' : ''}>${product.name}</option>`
                        ).join('')}
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                    <select name="items[${itemCounter}][unit_id]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 unit-select" required>
                        <option value="">Pilih Satuan</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                    <input type="number" name="items[${itemCounter}][quantity]" step="0.01" min="0.01" 
                           value="${quantity}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 quantity-input" 
                           onchange="updateItemTotal(this)" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga Satuan</label>
                    <input type="number" name="items[${itemCounter}][unit_cost]" step="0.01" min="0" 
                           value="${unitCost}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 price-input" 
                           onchange="updateItemTotal(this)" required>
                </div>
            </div>
            
            <div class="mt-4 text-right">
                <span class="text-sm text-gray-600">Subtotal: </span>
                <span class="font-medium text-lg item-subtotal">Rp 0</span>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    
    // If we have productId, update units and set the selected unit
    if (productId) {
        const productSelect = container.querySelector(`[data-index="${itemCounter}"] select[name*="product_id"]`);
        updateUnits(productSelect);
        
        // Set unit after a small delay to ensure options are loaded
        setTimeout(() => {
            const unitSelect = container.querySelector(`[data-index="${itemCounter}"] .unit-select`);
            unitSelect.value = unitId;
            updateItemTotal(container.querySelector(`[data-index="${itemCounter}"] .quantity-input`));
        }, 100);
    }
    
    itemCounter++;
}

function removeItem(button) {
    button.closest('.item-row').remove();
    updateTotal();
}

function updateUnits(productSelect) {
    const productId = productSelect.value;
    const unitSelect = productSelect.closest('.item-row').querySelector('.unit-select');
    
    unitSelect.innerHTML = '<option value="">Pilih Satuan</option>';
    
    if (productId) {
        const product = products.find(p => p.id == productId);
        if (product && product.product_units) {
            product.product_units.forEach(productUnit => {
                const option = document.createElement('option');
                option.value = productUnit.unit_id;
                option.textContent = productUnit.unit.name;
                unitSelect.appendChild(option);
            });
        }
    }
}

function updateItemTotal(input) {
    const row = input.closest('.item-row');
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const subtotal = quantity * price;
    
    row.querySelector('.item-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    updateTotal();
}

function updateTotal() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        subtotal += quantity * price;
    });
    
    const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const total = subtotal + tax - discount;
    
    document.getElementById('total-display').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// Load existing items on page load
document.addEventListener('DOMContentLoaded', function() {
    if (existingDetails.length > 0) {
        existingDetails.forEach(detail => {
            addItem(detail.product_id, detail.unit_id, detail.quantity, detail.unit_cost);
        });
    } else {
        addItem();
    }
    
    updateTotal();
});
</script>
@endsection