<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Transaksi - {{ $transaction->transaction_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: "Inter", sans-serif;
        }
        input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type="number"] {
    -moz-appearance: textfield;
}
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .quantity-control {
            display: flex;
            align-items: center;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .quantity-control button {
            padding: 0.5rem;
            background: #f9fafb;
            border: none;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
        }
        .quantity-control button:hover {
            background: #e5e7eb;
            color: #374151;
        }
        .quantity-control input {
            border: none;
            text-align: center;
            width: 4rem;
            padding: 0.5rem 0.25rem;
            background: white;
        }
        .quantity-control input:focus {
            outline: none;
            box-shadow: none;
        }
        .price-input {
            background: #fef3c7;
            border: 2px solid #fbbf24;
        }
        .price-input:focus {
            background: #fef3c7;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 p-4">

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Transaksi</h1>
                    <p class="text-gray-600">{{ $transaction->transaction_number }} - {{ $transaction->transaction_date->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('reports.sales') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('reports.transactions.update', $transaction) }}" method="POST" id="editTransactionForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Product Search & Cart -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Add New Product -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4">Tambah Produk</h2>
                        <div class="relative">
                            <input
                                type="text"
                                id="search-input"
                                placeholder="Scan barcode atau ketik nama produk..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                            <div class="absolute right-3 top-3">
                                <i id="search-icon" class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Search Results -->
                        <div id="search-results" class="mt-4 border border-gray-200 rounded-lg max-h-60 overflow-y-auto hidden">
                            <!-- Search results will be dynamically inserted here -->
                        </div>
                    </div>

                    <!-- Current Items -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold">Item Transaksi</h2>
                            <div class="text-sm text-gray-500">
                                <span id="item-count">{{ $transaction->details->count() }}</span> item
                            </div>
                        </div>

                        <div id="transaction-items" class="space-y-3">
                            @foreach($transaction->details as $index => $detail)
                            <div class="item-row flex items-center justify-between p-4 bg-gray-50 rounded-lg" data-index="{{ $index }}">
                                <!-- Hidden inputs -->
                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $detail->product_id }}">
                                <input type="hidden" name="items[{{ $index }}][unit_id]" value="{{ $detail->unit_id }}">
                                
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900">{{ $detail->product->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $detail->unit->name }}</p>
                                    <p class="text-xs text-gray-400">ID: {{ $detail->product->id }}</p>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <!-- Quantity Control -->
                                    <div class="quantity-control">
                                        <button type="button" onclick="updateQuantity({{ $index }}, -1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input
                                            type="number"
                                            name="items[{{ $index }}][quantity]"
                                            value="{{ $detail->quantity }}"
                                            class="quantity-input"
                                            step="0.1"
                                            min="0.1"
                                            onchange="calculateSubtotal({{ $index }})"
                                        />
                                        <button type="button" onclick="updateQuantity({{ $index }}, 1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Price Control -->
                                    <div class="flex flex-col items-center">
                                       
                                        <input
                                            type="number"
                                            name="items[{{ $index }}][unit_price]"
                                            value="{{ $detail->unit_price }}"
                                            class="price-input w-24 px-2 py-1 text-center rounded-md text-sm font-semibold"
                                            step="100"
                                            min="0"
                                            onchange="calculateSubtotal({{ $index }})"
                                        />
                                    </div>
                                    
                                    <!-- Subtotal -->
                                    <div class="text-right w-20">
                                        <p class="font-semibold subtotal-display">
                                            {{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </p>
                                        <p class="text-xs text-gray-500">Subtotal</p>
                                    </div>
                                    
                                    <!-- Remove Button -->
                                    <button
                                        type="button"
                                        class="text-red-600 hover:text-red-700 p-2"
                                        onclick="removeItem({{ $index }})"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div id="empty-message" class="text-center py-8 hidden">
                            <i class="fas fa-shopping-cart text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-500">Tidak ada item dalam transaksi</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Panel -->
                <div class="space-y-6">
                    <!-- Total Summary -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4">Ringkasan</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span id="subtotal-display" class="font-medium">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t pt-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span id="total-display" class="text-blue-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4">Pembayaran</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="paid-amount-input" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Dibayar
                                </label>
                                <input
                                    type="number"
                                    id="paid-amount-input"
                                    name="paid_amount"
                                    value="{{ $transaction->paid_amount }}"
                                    placeholder="0"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                                    onchange="calculateChange()"
                                />
                            </div>

                            <div id="change-display-container" class="p-3 bg-gray-50 rounded-lg">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Kembalian:</span>
                                    <span id="change-display" class="font-bold text-lg">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" class="quick-amount-button px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm font-medium" data-amount="50000">Rp50.000</button>
                                <button type="button" class="quick-amount-button px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm font-medium" data-amount="100000">Rp100.000</button>
                                <button type="button" class="quick-amount-button px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm font-medium" data-amount="200000">Rp200.000</button>
                                <button type="button" class="quick-amount-button px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm font-medium" data-amount="500000">Rp500.000</button>
                            </div>

                            <button
                                type="submit"
                                id="save-transaction-button"
                                class="w-full py-4 rounded-lg font-semibold text-lg bg-green-600 hover:bg-green-700 text-white"
                            >
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>

                    <!-- Transaction Info -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold mb-4">Info Transaksi</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">No. Transaksi:</span>
                                <span class="font-medium">{{ $transaction->transaction_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal:</span>
                                <span>{{ $transaction->transaction_date->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Global variables
        let itemIndex = {{ $transaction->details->count() }};
        let searchTimeout;
        let isSearching = false;
        const products = @json($products);

        // DOM Elements
        const searchInput = document.getElementById("search-input");
        const searchIcon = document.getElementById("search-icon");
        const searchResultsDiv = document.getElementById("search-results");
        const transactionItemsDiv = document.getElementById("transaction-items");
        const emptyMessage = document.getElementById("empty-message");
        const itemCountSpan = document.getElementById("item-count");
        const subtotalDisplay = document.getElementById("subtotal-display");
        const totalDisplay = document.getElementById("total-display");
        const paidAmountInput = document.getElementById("paid-amount-input");
        const changeDisplay = document.getElementById("change-display");
        const saveTransactionButton = document.getElementById("save-transaction-button");

        /**
         * Get CSRF token
         */
        function getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
        }

        /**
         * Shows loading indicator for search
         */
        function showSearchLoading() {
            isSearching = true;
            searchIcon.className = "loading-spinner";
        }

        /**
         * Hides loading indicator for search
         */
        function hideSearchLoading() {
            isSearching = false;
            searchIcon.className = "fas fa-search text-gray-400";
        }

        /**
         * Formats a number as Indonesian Rupiah currency
         */
        function formatCurrency(amount) {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0,
            }).format(amount);
        }

        /**
         * Format number for display
         */
        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        /**
         * Escapes HTML to prevent XSS
         */
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        /**
         * Updates quantity of an item
         */
        function updateQuantity(index, change) {
            const quantityInput = document.querySelector(`.item-row[data-index="${index}"] .quantity-input`);
            let currentQuantity = parseFloat(quantityInput.value) || 0;
            let newQuantity = currentQuantity + change;
            
            if (newQuantity < 0.1) {
                if (confirm('Hapus item ini dari transaksi?')) {
                    removeItem(index);
                }
                return;
            }
            
            quantityInput.value = newQuantity.toFixed(1);
            calculateSubtotal(index);
        }

        /**
         * Calculates subtotal for a specific item
         */
        function calculateSubtotal(index) {
            const itemRow = document.querySelector(`.item-row[data-index="${index}"]`);
            const quantityInput = itemRow.querySelector('.quantity-input');
            const priceInput = itemRow.querySelector('.price-input');
            const subtotalDisplay = itemRow.querySelector('.subtotal-display');
            
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const subtotal = quantity * price;
            
            subtotalDisplay.textContent = formatNumber(subtotal);
            updateTotals();
        }

        /**
         * Updates the total calculations
         */
        function updateTotals() {
            let subtotal = 0;
            let itemCount = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                const quantityInput = row.querySelector('.quantity-input');
                const priceInput = row.querySelector('.price-input');
                
                const quantity = parseFloat(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                subtotal += quantity * price;
                itemCount++;
            });

            subtotalDisplay.textContent = formatCurrency(subtotal);
            totalDisplay.textContent = formatCurrency(subtotal);
            itemCountSpan.textContent = itemCount;

            // Show/hide empty message
            if (itemCount === 0) {
                emptyMessage.classList.remove('hidden');
                transactionItemsDiv.classList.add('hidden');
            } else {
                emptyMessage.classList.add('hidden');
                transactionItemsDiv.classList.remove('hidden');
            }

            calculateChange();
        }

        /**
         * Calculates change amount
         */
        function calculateChange() {
            const totalText = totalDisplay.textContent.replace(/[^\d]/g, '');
            const total = parseFloat(totalText) || 0;
            const paid = parseFloat(paidAmountInput.value) || 0;
            const change = paid - total;

            changeDisplay.textContent = formatCurrency(Math.max(0, change));
            changeDisplay.className = `font-bold text-lg ${change >= 0 ? "text-green-600" : "text-red-600"}`;
        }

        /**
         * Removes an item from the transaction
         */
        function removeItem(index) {
            const itemRow = document.querySelector(`.item-row[data-index="${index}"]`);
            if (itemRow) {
                const productName = itemRow.querySelector('h4').textContent;
                
                Swal.fire({
                    title: 'Hapus Item?',
                    text: `Hapus ${productName} dari transaksi?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        itemRow.remove();
                        updateTotals();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Item Dihapus',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                });
            }
        }

        /**
         * Handles product search
         */
        async function handleSearch(query) {
            if (query.length < 2) {
                searchResultsDiv.innerHTML = "";
                searchResultsDiv.classList.add("hidden");
                return;
            }

            showSearchLoading();

            try {
                // Use products data from PHP instead of API call
                const filteredResults = products.filter(product => 
                    product.name.toLowerCase().includes(query.toLowerCase()) ||
                    (product.barcode && product.barcode.includes(query))
                );

                if (filteredResults.length > 0) {
                    searchResultsDiv.classList.remove("hidden");
                    searchResultsDiv.innerHTML = filteredResults.map(product => `
                        <div class="p-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">${escapeHtml(product.name)}</h4>
                                    <p class="text-sm text-gray-500">${escapeHtml(product.category?.name || 'Tanpa Kategori')}</p>
                                    ${product.barcode ? `<p class="text-xs text-gray-400">Barcode: ${escapeHtml(product.barcode)}</p>` : ''}
                                </div>
                                <div class="ml-4">
                                    <div class="space-y-1">
                                        ${product.product_units ? product.product_units.map(unit => `
                                            <button
                                                type="button"
                                                class="block w-full text-right bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded text-sm transition-colors"
                                                onclick="addItemToTransaction(${product.id}, '${escapeHtml(product.name)}', '${unit.unit_id}', '${escapeHtml(unit.unit.name)}', ${unit.price})"
                                            >
                                                <div class="font-medium">${formatCurrency(unit.price)}</div>
                                                <div class="text-xs text-gray-600">per ${escapeHtml(unit.unit.name)}</div>
                                            </button>
                                        `).join('') : '<p class="text-xs text-gray-500">Tidak ada unit</p>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    searchResultsDiv.innerHTML = `<div class="p-3 text-center text-gray-500">Tidak ada produk ditemukan untuk "${escapeHtml(query)}"</div>`;
                    searchResultsDiv.classList.remove("hidden");
                }

            } catch (error) {
                console.error("Search error:", error);
                searchResultsDiv.innerHTML = `<div class="p-3 text-center text-red-500">Terjadi kesalahan saat mencari produk</div>`;
                searchResultsDiv.classList.remove("hidden");
            } finally {
                hideSearchLoading();
            }
        }

        /**
         * Adds a new item to the transaction
         */
        function addItemToTransaction(productId, productName, unitId, unitName, price) {
            // Check if item already exists
            const existingItem = document.querySelector(`input[name*="[product_id]"][value="${productId}"]`);
            if (existingItem) {
                const itemRow = existingItem.closest('.item-row');
                const index = itemRow.dataset.index;
                const quantityInput = itemRow.querySelector('.quantity-input');
                const currentQuantity = parseFloat(quantityInput.value) || 0;
                quantityInput.value = (currentQuantity + 1).toFixed(1);
                calculateSubtotal(index);
                
                // Show feedback
                Swal.fire({
                    icon: 'info',
                    title: 'Quantity Updated',
                    text: `${productName} quantity increased`,
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                
                // Clear search
                searchInput.value = "";
                searchResultsDiv.classList.add("hidden");
                return;
            }

            // Add new item
            const itemHtml = `
                <div class="item-row flex items-center justify-between p-4 bg-gray-50 rounded-lg" data-index="${itemIndex}">
                    <!-- Hidden inputs -->
                    <input type="hidden" name="items[${itemIndex}][product_id]" value="${productId}">
                    <input type="hidden" name="items[${itemIndex}][unit_id]" value="${unitId}">
                    
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900">${escapeHtml(productName)}</h4>
                        <p class="text-sm text-gray-500">${escapeHtml(unitName)}</p>
                        <p class="text-xs text-gray-400">ID: ${productId}</p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Quantity Control -->
                        <div class="quantity-control">
                            <button type="button" onclick="updateQuantity(${itemIndex}, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input
                                type="number"
                                name="items[${itemIndex}][quantity]"
                                value="1.0"
                                class="quantity-input"
                                step="0.1"
                                min="0.1"
                                onchange="calculateSubtotal(${itemIndex})"
                            />
                            <button type="button" onclick="updateQuantity(${itemIndex}, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        
                        <!-- Price Control -->
                        <div class="flex flex-col items-center">
                            <label class="text-xs text-amber-600 font-medium mb-1">Harga</label>
                            <input
                                type="number"
                                name="items[${itemIndex}][unit_price]"
                                value="${price}"
                                class="price-input w-24 px-2 py-1 text-center rounded-md text-sm font-semibold"
                                step="100"
                                min="0"
                                onchange="calculateSubtotal(${itemIndex})"
                            />
                        </div>
                        
                        <!-- Subtotal -->
                        <div class="text-right w-20">
                            <p class="font-semibold subtotal-display">
                                ${formatNumber(price)}
                            </p>
                            <p class="text-xs text-gray-500">Subtotal</p>
                        </div>
                        
                        <!-- Remove Button -->
                        <button
                            type="button"
                            class="text-red-600 hover:text-red-700 p-2"
                            onclick="removeItem(${itemIndex})"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;

            transactionItemsDiv.insertAdjacentHTML('beforeend', itemHtml);
            itemIndex++;
            updateTotals();

            // Clear search
            searchInput.value = "";
            searchResultsDiv.classList.add("hidden");
            searchInput.focus();

            // Show success feedback
            Swal.fire({
                icon: 'success',
                title: 'Item Ditambahkan',
                text: `${productName} ditambahkan ke transaksi`,
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // Event Listeners
        document.addEventListener("DOMContentLoaded", () => {
            updateTotals();

            // Search input handling with debounce
            searchInput.addEventListener("input", (e) => {
                const query = e.target.value;
                clearTimeout(searchTimeout);
                
                if (query.length === 0) {
                    searchResultsDiv.innerHTML = "";
                    searchResultsDiv.classList.add("hidden");
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    handleSearch(query);
                }, 300);
            });

            // Handle Enter key press on search input
            searchInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    const query = searchInput.value;
                    if (query) {
                        clearTimeout(searchTimeout);
                        handleSearch(query);
                    }
                }
            });

            // Quick amount buttons
            document.querySelectorAll(".quick-amount-button").forEach(button => {
                button.addEventListener("click", (e) => {
                    paidAmountInput.value = e.target.dataset.amount;
                    calculateChange();
                });
            });

            // Close search results when clicking outside
            document.addEventListener("click", (e) => {
                if (!searchInput.contains(e.target) && !searchResultsDiv.contains(e.target)) {
                    searchResultsDiv.classList.add("hidden");
                }
            });

            // Form validation
            document.getElementById('editTransactionForm').addEventListener('submit', function(e) {
                const items = document.querySelectorAll('.item-row');
                
                if (items.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Minimal harus ada satu item dalam transaksi'
                    });
                    return;
                }
                
                let hasValidItem = false;
                items.forEach(item => {
                    const productIdInput = item.querySelector('input[name*="[product_id]"]');
                    const unitIdInput = item.querySelector('input[name*="[unit_id]"]');
                    const quantityInput = item.querySelector('.quantity-input');
                    
                    if (productIdInput.value && unitIdInput.value && parseFloat(quantityInput.value) > 0) {
                        hasValidItem = true;
                    }
                });
                
                if (!hasValidItem) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Pastikan semua item memiliki data yang valid'
                    });
                    return;
                }
                
                const totalText = totalDisplay.textContent.replace(/[^\d]/g, '');
                const total = parseFloat(totalText) || 0;
                const paid = parseFloat(paidAmountInput.value) || 0;
                
                if (paid < total) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Jumlah pembayaran kurang dari total transaksi'
                    });
                    return;
                }

                // Show confirmation before saving
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    html: `
                        <div class="text-left">
                            <p><strong>Total: ${totalDisplay.textContent}</strong></p>
                            <p>Dibayar: ${formatCurrency(paid)}</p>
                            <p>Kembalian: ${changeDisplay.textContent}</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Simpan Perubahan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        saveTransactionButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
                        saveTransactionButton.disabled = true;
                        
                        // Submit form
                        document.getElementById('editTransactionForm').submit();
                    }
                });
            });

            // Keyboard shortcuts
            document.addEventListener("keydown", (e) => {
                // Ctrl/Cmd + F for focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    searchInput.focus();
                }
                
                // F1 for focus search
                if (e.key === 'F1') {
                    e.preventDefault();
                    searchInput.focus();
                }
                
                // F2 for focus paid amount
                if (e.key === 'F2') {
                    e.preventDefault();
                    paidAmountInput.focus();
                }
                
                // F3 for save (if form is valid)
                if (e.key === 'F3') {
                    e.preventDefault();
                    document.getElementById('editTransactionForm').dispatchEvent(new Event('submit'));
                }
                
                // ESC to clear search
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    searchResultsDiv.classList.add('hidden');
                }
            });

            // Initialize focus on search input
            searchInput.focus();
        });

        // Utility function to handle price input formatting
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('price-input')) {
                // Auto-format price input (remove this if not needed)
                let value = e.target.value.replace(/[^\d]/g, '');
                if (value) {
                    // You can add thousand separators here if needed
                    e.target.value = value;
                }
            }
        });

        // Add visual feedback for price changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('price-input')) {
                // Add temporary highlight effect
                e.target.style.backgroundColor = '#fef3c7';
                e.target.style.borderColor = '#f59e0b';
                setTimeout(() => {
                    e.target.style.backgroundColor = '#fef3c7';
                    e.target.style.borderColor = '#fbbf24';
                }, 200);
            }
        });

        // Double-click to select all in price inputs
        document.addEventListener('dblclick', function(e) {
            if (e.target.classList.contains('price-input') || e.target.classList.contains('quantity-input')) {
                e.target.select();
            }
        });
    </script>
</body>
</html>