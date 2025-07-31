@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Transaksi</h1>
            <p class="text-gray-600">{{ $transaction->transaction_number }} - {{ $transaction->transaction_date->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('reports.sales') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('reports.transactions.update', $transaction) }}" method="POST" id="editTransactionForm">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Item Transaksi</h3>
                            <button type="button" onclick="addItem()" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>Tambah Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="itemsList" class="space-y-4">
                            @foreach($transaction->details as $index => $detail)
                            <div class="item-row bg-gray-50 p-4 rounded-lg" data-index="{{ $index }}">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="form-label">Produk</label>
                                        {{-- HIDDEN INPUT FOR PRODUCT ID --}}
                                        <input type="hidden" name="items[{{ $index }}][product_id]" class="product-id-input" value="{{ $detail->product_id }}">
                                        {{-- SEARCHABLE PRODUCT INPUT --}}
                                        <input type="text" 
                                               class="form-input product-search-input" 
                                               placeholder="Cari Produk..."
                                               oninput="searchProducts(this, {{ $index }})" 
                                               value="{{ $detail->product->name }}">
                                        {{-- PRODUCT SEARCH RESULTS CONTAINER --}}
                                        <div class="product-search-results mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg absolute z-10 hidden"></div>
                                    </div>
                                    <div>
                                        <label class="form-label">Satuan</label>
                                        <select name="items[{{ $index }}][unit_id]" class="form-input unit-select" onchange="updatePrice({{ $index }})">
                                            @foreach($detail->product->productUnits as $productUnit)
                                            <option value="{{ $productUnit->unit_id }}" 
                                                    data-price="{{ $productUnit->price }}" 
                                                    {{ $detail->unit_id == $productUnit->unit_id ? 'selected' : '' }}>
                                                {{ $productUnit->unit->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">Jumlah</label>
                                        <input type="number" 
                                               name="items[{{ $index }}][quantity]" 
                                               value="{{ $detail->quantity }}"
                                               class="form-input quantity-input" 
                                               step="0.01" 
                                               min="0.01"
                                               onchange="calculateSubtotal({{ $index }})">
                                    </div>
                                    <div>
                                        <label class="form-label">Harga</label>
                                        <input type="number" 
                                               name="items[{{ $index }}][unit_price]" 
                                               value="{{ $detail->unit_price }}"
                                               class="form-input price-input" 
                                               step="0.01" 
                                               min="0"
                                               onchange="calculateSubtotal({{ $index }})">
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mt-4">
                                    <div class="text-lg font-semibold">
                                        Subtotal: Rp <span class="subtotal-display">{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <button type="button" onclick="removeItem({{ $index }})" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="card sticky top-4">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan</h3>
                    </div>
                    <div class="card-body space-y-4">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium" id="totalSubtotal">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-semibold border-t pt-2">
                                <span>Total:</span>
                                <span id="grandTotal">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Jumlah Dibayar</label>
                            <input type="number" 
                                   name="paid_amount" 
                                   value="{{ $transaction->paid_amount }}"
                                   class="form-input" 
                                   step="0.01" 
                                   min="0"
                                   id="paidAmount"
                                   onchange="calculateChange()">
                        </div>

                        <div class="flex justify-between text-lg">
                            <span>Kembalian:</span>
                            <span id="changeAmount" class="font-semibold">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit" class="btn-primary w-full">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>

                        <div class="mt-6 pt-4 border-t">
                            <h4 class="font-semibold text-gray-900 mb-2">Info Transaksi</h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>No:</strong> {{ $transaction->transaction_number }}</p>
                                <p><strong>Tanggal:</strong> {{ $transaction->transaction_date->format('d/m/Y H:i') }}</p>
                                <p><strong>Status:</strong> {{ ucfirst($transaction->status) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let itemIndex = {{ $transaction->details->count() }};
const products = @json($products);

function addItem() {
    const itemsList = document.getElementById('itemsList');
    
    const itemHtml = `
        <div class="item-row bg-gray-50 p-4 rounded-lg" data-index="${itemIndex}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2 relative"> {{-- Added relative for positioning search results --}}
                    <label class="form-label">Produk</label>
                    <input type="hidden" name="items[${itemIndex}][product_id]" class="product-id-input">
                    <input type="text" 
                           class="form-input product-search-input" 
                           placeholder="Cari Produk..."
                           oninput="searchProducts(this, ${itemIndex})">
                    <div class="product-search-results mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg absolute z-10 hidden"></div>
                </div>
                <div>
                    <label class="form-label">Satuan</label>
                    <select name="items[${itemIndex}][unit_id]" class="form-input unit-select" onchange="updatePrice(${itemIndex})">
                        <option value="">Pilih Satuan</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Jumlah</label>
                    <input type="number" 
                           name="items[${itemIndex}][quantity]" 
                           value="1"
                           class="form-input quantity-input" 
                           step="0.01" 
                           min="0.01"
                           onchange="calculateSubtotal(${itemIndex})">
                </div>
                <div>
                    <label class="form-label">Harga</label>
                    <input type="number" 
                           name="items[${itemIndex}][unit_price]" 
                           value="0"
                           class="form-input price-input" 
                           step="0.01" 
                           min="0"
                           onchange="calculateSubtotal(${itemIndex})">
                </div>
            </div>
            <div class="flex justify-between items-center mt-4">
                <div class="text-lg font-semibold">
                    Subtotal: Rp <span class="subtotal-display">0</span>
                </div>
                <button type="button" onclick="removeItem(${itemIndex})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    itemsList.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
}

function removeItem(index) {
    const itemRow = document.querySelector(`.item-row[data-index="${index}"]`);
    if (itemRow) {
        itemRow.remove();
        calculateTotals();
    }
}

function searchProducts(inputElement, index) {
    const searchTerm = inputElement.value.toLowerCase();
    const resultsContainer = document.querySelector(`.item-row[data-index="${index}"] .product-search-results`);
    resultsContainer.innerHTML = '';
    
    if (searchTerm.length < 2) { // Require at least 2 characters to start searching
        resultsContainer.classList.add('hidden');
        return;
    }

    const filteredProducts = products.filter(product => 
        product.name.toLowerCase().includes(searchTerm)
    );

    if (filteredProducts.length > 0) {
        filteredProducts.forEach(product => {
            const resultItem = document.createElement('div');
            resultItem.classList.add('p-2', 'cursor-pointer', 'hover:bg-gray-100');
            resultItem.textContent = product.name;
            resultItem.onclick = () => selectProduct(product, index);
            resultsContainer.appendChild(resultItem);
        });
        resultsContainer.classList.remove('hidden');
    } else {
        resultsContainer.classList.add('hidden');
    }
}

function selectProduct(product, index) {
    const itemRow = document.querySelector(`.item-row[data-index="${index}"]`);
    const searchInput = itemRow.querySelector('.product-search-input');
    const productIdInput = itemRow.querySelector('.product-id-input');
    const resultsContainer = itemRow.querySelector('.product-search-results');

    searchInput.value = product.name;
    productIdInput.value = product.id;
    resultsContainer.classList.add('hidden');

    loadProductUnits(product.id, index);
}

// Modified loadProductUnits to accept product ID directly
function loadProductUnits(productId, index) {
    const unitSelect = document.querySelector(`.item-row[data-index="${index}"] .unit-select`);
    unitSelect.innerHTML = '<option value="">Pilih Satuan</option>'; // Clear existing options

    if (!productId) {
        return;
    }
    
    const product = products.find(p => p.id == productId);
    if (product && product.product_units) {
        unitSelect.innerHTML += 
            product.product_units.map(pu => 
                `<option value="${pu.unit_id}" data-price="${pu.price}">${pu.unit.name}</option>`
            ).join('');
    }
    // Automatically select the first unit if available and update price
    if (unitSelect.options.length > 1) {
        unitSelect.selectedIndex = 1; // Select the first actual unit (index 1 after "Pilih Satuan")
        updatePrice(index);
    } else {
        // If no units are available, ensure price is 0
        const priceInput = document.querySelector(`.item-row[data-index="${index}"] .price-input`);
        priceInput.value = 0;
        calculateSubtotal(index);
    }
}

function updatePrice(index) {
    const unitSelect = document.querySelector(`.item-row[data-index="${index}"] .unit-select`);
    const priceInput = document.querySelector(`.item-row[data-index="${index}"] .price-input`);
    
    const selectedOption = unitSelect.options[unitSelect.selectedIndex];
    if (selectedOption && selectedOption.dataset.price) {
        priceInput.value = selectedOption.dataset.price;
        calculateSubtotal(index);
    } else {
        priceInput.value = 0; // If no unit is selected or price not found
        calculateSubtotal(index);
    }
}

function calculateSubtotal(index) {
    const quantityInput = document.querySelector(`.item-row[data-index="${index}"] .quantity-input`);
    const priceInput = document.querySelector(`.item-row[data-index="${index}"] .price-input`);
    const subtotalDisplay = document.querySelector(`.item-row[data-index="${index}"] .subtotal-display`);
    
    const quantity = parseFloat(quantityInput.value) || 0;
    const price = parseFloat(priceInput.value) || 0;
    const subtotal = quantity * price;
    
    subtotalDisplay.textContent = formatNumber(subtotal);
    calculateTotals();
}

function calculateTotals() {
    let totalSubtotal = 0;
    
    document.querySelectorAll('.item-row').forEach(row => {
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        totalSubtotal += quantity * price;
    });
    
    document.getElementById('totalSubtotal').textContent = 'Rp ' + formatNumber(totalSubtotal);
    document.getElementById('grandTotal').textContent = 'Rp ' + formatNumber(totalSubtotal);
    
    calculateChange();
}

function calculateChange() {
    const grandTotalText = document.getElementById('grandTotal').textContent;
    const grandTotal = parseFloat(grandTotalText.replace(/[^\d.,]/g, '')) || 0; // handle thousands separators
    const paidAmount = parseFloat(document.getElementById('paidAmount').value) || 0;
    const change = paidAmount - grandTotal;
    
    document.getElementById('changeAmount').textContent = 'Rp ' + formatNumber(Math.max(0, change));
}

function formatNumber(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

// Form validation
document.getElementById('editTransactionForm').addEventListener('submit', function(e) {
    const items = document.querySelectorAll('.item-row');
    
    if (items.length === 0) {
        e.preventDefault();
        showAlert('error', 'Minimal harus ada satu item dalam transaksi');
        return;
    }
    
    let hasValidItem = false;
    items.forEach(item => {
        const productIdInput = item.querySelector('.product-id-input'); // Use hidden input
        const unitSelect = item.querySelector('.unit-select');
        const quantityInput = item.querySelector('.quantity-input');
        
        if (productIdInput.value && unitSelect.value && parseFloat(quantityInput.value) > 0) {
            hasValidItem = true;
        }
    });
    
    if (!hasValidItem) {
        e.preventDefault();
        showAlert('error', 'Pastikan semua item memiliki produk, satuan, dan jumlah yang valid');
        return;
    }
    
    const grandTotalText = document.getElementById('grandTotal').textContent;
    const grandTotal = parseFloat(grandTotalText.replace(/[^\d.,]/g, '')) || 0;
    const paidAmount = parseFloat(document.getElementById('paidAmount').value) || 0;
    
    if (paidAmount < grandTotal) {
        e.preventDefault();
        showAlert('error', 'Jumlah pembayaran kurang dari total transaksi');
        return;
    }
});

// Close search results when clicking outside
document.addEventListener('click', function(event) {
    document.querySelectorAll('.product-search-results').forEach(resultsContainer => {
        const itemRow = resultsContainer.closest('.item-row');
        const searchInput = itemRow.querySelector('.product-search-input');
        if (!searchInput.contains(event.target) && !resultsContainer.contains(event.target)) {
            resultsContainer.classList.add('hidden');
        }
    });
});

// Initialize calculations
document.addEventListener('DOMContentLoaded', function() {
    calculateTotals();
    // For existing items, ensure units are loaded and prices updated based on pre-selected product
    document.querySelectorAll('.item-row').forEach((row, index) => {
        const productId = row.querySelector('.product-id-input').value;
        if (productId) {
            // No need to call loadProductUnits here for initial load as data is already populated by PHP
            // However, if you want to re-evaluate pricing based on currently selected unit, call updatePrice
            updatePrice(index);
        }
    });
});
</script>
@endsection