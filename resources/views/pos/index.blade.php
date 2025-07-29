<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Point of Sale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: "Inter", sans-serif;
        }
        .custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .custom-modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
            text-align: center;
        }
        .custom-modal-button {
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .custom-modal-button:hover {
            background-color: #2563eb;
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
    </style>
</head>
<body class="min-h-screen bg-gray-50 p-4">

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Point of Sale</h1>
                    <p class="text-gray-600">Kasir Toko Kelontong</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Tanggal</p>
                    <p class="text-lg font-semibold" id="current-date"></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Product Search & Cart -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Search Product -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Cari Produk</h2>
                    <div class="relative">
                        <input
                            type="text"
                            id="search-input"
                            placeholder="Scan barcode atau ketik nama produk..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            autofocus
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

                <!-- Shopping Cart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Keranjang Belanja</h2>
                        <button
                            id="clear-cart-button"
                            class="text-red-600 hover:text-red-700 text-sm hidden"
                        >
                            <i class="fas fa-trash mr-1"></i>
                            Kosongkan
                        </button>
                    </div>

                    <div id="cart-empty-message" class="text-center py-8">
                        <i class="fas fa-shopping-cart text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500">Keranjang masih kosong</p>
                    </div>

                    <div id="cart-items" class="space-y-3">
                        <!-- Cart items will be dynamically inserted here -->
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
                            <span id="subtotal-display" class="font-medium">Rp0</span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span id="total-display" class="text-blue-600">Rp0</span>
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
                                placeholder="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                            />
                        </div>

                        <div id="change-display-container" class="p-3 bg-gray-50 rounded-lg hidden">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kembalian:</span>
                                <span id="change-display" class="font-bold text-lg">Rp0</span>
                            </div>
                        </div>

                        <!-- Quick Amount Buttons -->
                        <div class="grid grid-cols-2 gap-2">
                            <button class="quick-amount-button px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm font-medium" data-amount="50000">Rp50.000</button>
                            <button class="quick-amount-button px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm font-medium" data-amount="100000">Rp100.000</button>
                            <button class="quick-amount-button px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm font-medium" data-amount="200000">Rp200.000</button>
                            <button class="quick-amount-button px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm font-medium" data-amount="500000">Rp500.000</button>
                        </div>

                        <button
                            id="process-transaction-button"
                            class="w-full py-4 rounded-lg font-semibold text-lg bg-gray-300 text-gray-500 cursor-not-allowed"
                            disabled
                        >
                            <i class="fas fa-cash-register mr-2"></i>
                            Proses Transaksi
                        </button>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Aksi Cepat</h3>
                    <div class="space-y-2">
                        <button
                            id="focus-search-button"
                            class="w-full px-4 py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg font-medium"
                        >
                            <i class="fas fa-search mr-2"></i>
                            Fokus Pencarian
                        </button>
                        <button
                            onclick="window.location.href = '/products'"
                            class="w-full px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg font-medium"
                        >
                            <i class="fas fa-box mr-2"></i>
                            Kelola Produk
                        </button>
                        <button
                            onclick="window.location.href = '/stock'"
                            class="w-full px-4 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 rounded-lg font-medium"
                        >
                            <i class="fas fa-warehouse mr-2"></i>
                            Kelola Stok
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Alert Modal -->
    <div id="custom-alert-modal" class="custom-modal-overlay hidden">
        <div class="custom-modal-content">
            <p id="custom-alert-message" class="text-gray-800 text-lg"></p>
            <button id="custom-alert-ok-button" class="custom-modal-button">OK</button>
        </div>
    </div>

    <script>
        // Global state variables
        let cart = [];
        let searchQuery = "";
        let paidAmount = "";
        let isProcessingTransaction = false;
        let searchTimeout;
        let isSearching = false;

        // DOM Elements
        const searchInput = document.getElementById("search-input");
        const searchIcon = document.getElementById("search-icon");
        const searchResultsDiv = document.getElementById("search-results");
        const cartItemsDiv = document.getElementById("cart-items");
        const cartEmptyMessage = document.getElementById("cart-empty-message");
        const clearCartButton = document.getElementById("clear-cart-button");
        const subtotalDisplay = document.getElementById("subtotal-display");
        const totalDisplay = document.getElementById("total-display");
        const paidAmountInput = document.getElementById("paid-amount-input");
        const changeDisplayContainer = document.getElementById("change-display-container");
        const changeDisplay = document.getElementById("change-display");
        const processTransactionButton = document.getElementById("process-transaction-button");
        const focusSearchButton = document.getElementById("focus-search-button");
        const customAlertModal = document.getElementById("custom-alert-modal");
        const customAlertMessage = document.getElementById("custom-alert-message");
        const customAlertOkButton = document.getElementById("custom-alert-ok-button");
        const currentDateElement = document.getElementById("current-date");

        /**
         * Initialize date display
         */
        function initializeDate() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                timeZone: 'Asia/Jakarta'
            };
            currentDateElement.textContent = now.toLocaleDateString('id-ID', options);
        }

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
         * Displays a custom alert modal
         */
        function showAlert(message) {
            customAlertMessage.textContent = message;
            customAlertModal.classList.remove("hidden");
        }

        /**
         * Hides the custom alert modal
         */
        function hideAlert() {
            customAlertModal.classList.add("hidden");
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
         * Renders the cart items in the DOM
         */
        function renderCart() {
            if (cart.length === 0) {
                cartEmptyMessage.classList.remove("hidden");
                cartItemsDiv.innerHTML = "";
                clearCartButton.classList.add("hidden");
            } else {
                cartEmptyMessage.classList.add("hidden");
                clearCartButton.classList.remove("hidden");
                cartItemsDiv.innerHTML = cart.map((item) => `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">${escapeHtml(item.name)}</h4>
                            <p class="text-sm text-gray-500">
                                ${formatCurrency(item.price)} per ${escapeHtml(item.unit_symbol)}
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center space-x-2">
                                <button
                                    class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center"
                                    onclick="updateQuantity('${item.id}', ${item.quantity - 1})"
                                >
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <input
                                    type="number"
                                    value="${item.quantity}"
                                    onchange="updateQuantity('${item.id}', parseFloat(this.value) || 0)"
                                    class="w-16 text-center border border-gray-300 rounded px-2 py-1"
                                    min="0"
                                    step="0.1"
                                />
                                <button
                                    class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center"
                                    onclick="updateQuantity('${item.id}', ${item.quantity + 1})"
                                >
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            <div class="w-20 text-right">
                                <p class="font-semibold">
                                    ${formatCurrency(item.quantity * item.price)}
                                </p>
                            </div>
                            <button
                                class="text-red-600 hover:text-red-700 p-1"
                                onclick="removeFromCart('${item.id}')"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `).join("");
            }
            updateTotals();
        }

        /**
         * Updates the subtotal, total, and change displays
         */
        function updateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + item.quantity * item.price, 0);
            const total = subtotal;
            const change = parseFloat(paidAmount || 0) - total;

            subtotalDisplay.textContent = formatCurrency(subtotal);
            totalDisplay.textContent = formatCurrency(total);

            if (paidAmount) {
                changeDisplayContainer.classList.remove("hidden");
                changeDisplay.textContent = formatCurrency(change);
                changeDisplay.className = `font-bold text-lg ${change >= 0 ? "text-green-600" : "text-red-600"}`;
            } else {
                changeDisplayContainer.classList.add("hidden");
            }

            // Update process transaction button state
            if (cart.length === 0 || parseFloat(paidAmount || 0) < total || isProcessingTransaction) {
                processTransactionButton.disabled = true;
                processTransactionButton.classList.remove("bg-blue-600", "hover:bg-blue-700", "text-white");
                processTransactionButton.classList.add("bg-gray-300", "text-gray-500", "cursor-not-allowed");
            } else {
                processTransactionButton.disabled = false;
                processTransactionButton.classList.remove("bg-gray-300", "text-gray-500", "cursor-not-allowed");
                processTransactionButton.classList.add("bg-blue-600", "hover:bg-blue-700", "text-white");
            }
        }

        /**
         * Handles the product search
         */
        async function handleSearch(query) {
            if (query.length < 2) {
                searchResultsDiv.innerHTML = "";
                searchResultsDiv.classList.add("hidden");
                return;
            }

            showSearchLoading();

            try {
                const response = await fetch(`/api/pos/search-product?query=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                // Handle error response
                if (data.error) {
                    throw new Error(data.message || 'Terjadi kesalahan saat mencari produk');
                }

                const filteredResults = Array.isArray(data) ? data : [];

                if (filteredResults.length > 0) {
                    searchResultsDiv.classList.remove("hidden");
                    searchResultsDiv.innerHTML = filteredResults.map(product => `
                        <div class="p-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">${escapeHtml(product.name)}</h4>
                                    <p class="text-sm text-gray-500">${escapeHtml(product.category)}</p>
                                    ${product.barcode ? `<p class="text-xs text-gray-400">Barcode: ${escapeHtml(product.barcode)}</p>` : ''}
                                    ${product.stock_info && product.stock_info.length > 0 ?
                                        `<p class="text-xs text-gray-500">Stok: ${product.stock_info.map(s => `${s.quantity} ${escapeHtml(s.unit_symbol)}`).join(', ')}</p>` :
                                        `<p class="text-xs text-gray-500">Stok: N/A</p>`
                                    }
                                </div>
                                <div class="ml-4">
                                    <div class="space-y-1">
                                        ${product.units.map(unit => `
                                            <button
                                                class="block w-full text-right bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded text-sm transition-colors"
                                                onclick="addToCart(${escapeHtml(JSON.stringify(product))}, '${unit.unit_id}', ${unit.price}, '${escapeHtml(unit.unit_symbol)}')"
                                            >
                                                <div class="font-medium">${formatCurrency(unit.price)}</div>
                                                <div class="text-xs text-gray-600">per ${escapeHtml(unit.unit_symbol)}</div>
                                            </button>
                                        `).join('')}
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
                showAlert("Terjadi kesalahan saat mencari produk: " + error.message);
                searchResultsDiv.innerHTML = `<div class="p-3 text-center text-red-500">Terjadi kesalahan saat mencari produk</div>`;
                searchResultsDiv.classList.remove("hidden");
            } finally {
                hideSearchLoading();
            }
        }

        /**
         * Adds a product to the cart
         */
        function addToCart(product, unitId, unitPrice, unitSymbol) {
            try {
                // Parse product if it's a string (from onclick)
                if (typeof product === 'string') {
                    product = JSON.parse(product);
                }

                const existingItemIndex = cart.findIndex(
                    (item) => item.product_id === product.id && item.unit_id === unitId
                );

                if (existingItemIndex >= 0) {
                    cart[existingItemIndex].quantity += 1;
                } else {
                    const newItem = {
                        id: Date.now() + Math.random(), // Unique ID
                        product_id: product.id,
                        unit_id: unitId,
                        name: product.name,
                        unit_symbol: unitSymbol,
                        price: parseFloat(unitPrice),
                        quantity: 1,
                    };
                    cart.push(newItem);
                }

                // Clear search
                searchQuery = "";
                searchInput.value = "";
                searchResultsDiv.innerHTML = "";
                searchResultsDiv.classList.add("hidden");
                searchInput.focus();
                renderCart();
                
                // Show success feedback
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: `${product.name} ditambahkan ke keranjang`,
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });

            } catch (error) {
                console.error("Error adding to cart:", error);
                showAlert("Terjadi kesalahan saat menambahkan produk ke keranjang");
            }
        }

        /**
         * Updates the quantity of an item in the cart
         */
        function updateQuantity(itemId, newQuantity) {
            if (newQuantity <= 0) {
                removeFromCart(itemId);
                return;
            }

            cart = cart.map((item) =>
                item.id == itemId ? { ...item, quantity: newQuantity } : item
            );
            renderCart();
        }

        /**
         * Removes an item from the cart
         */
        function removeFromCart(itemId) {
            const itemName = cart.find(item => item.id == itemId)?.name;
            cart = cart.filter((item) => item.id != itemId);
            renderCart();
            
            if (itemName) {
                Swal.fire({
                    icon: 'info',
                    title: 'Dihapus',
                    text: `${itemName} dihapus dari keranjang`,
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        }

        /**
         * Clears the entire cart
         */
        function clearCart() {
            Swal.fire({
                title: 'Kosongkan Keranjang?',
                text: "Semua item akan dihapus dari keranjang",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Kosongkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    paidAmount = "";
                    paidAmountInput.value = "";
                    renderCart();
                    updateTotals();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Keranjang Dikosongkan',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
        }

        /**
         * Processes the transaction
         */
        async function processTransaction() {
            if (cart.length === 0) {
                showAlert("Keranjang masih kosong!");
                return;
            }

            const subtotal = cart.reduce((sum, item) => sum + item.quantity * item.price, 0);
            const total = subtotal;
            
            if (parseFloat(paidAmount) < total) {
                showAlert("Jumlah pembayaran kurang!");
                paidAmountInput.focus();
                return;
            }

            // Show confirmation dialog
            const result = await Swal.fire({
                title: 'Konfirmasi Transaksi',
                html: `
                    <div class="text-left">
                        <p><strong>Total: ${formatCurrency(total)}</strong></p>
                        <p>Dibayar: ${formatCurrency(parseFloat(paidAmount))}</p>
                        <p>Kembalian: ${formatCurrency(parseFloat(paidAmount) - total)}</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Proses Transaksi',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) {
                return;
            }

            isProcessingTransaction = true;
            processTransactionButton.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...`;
            updateTotals();

            try {
                const response = await fetch("/api/pos/process", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCSRFToken(),
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        items: cart.map((item) => ({
                            product_id: item.product_id,
                            unit_id: item.unit_id,
                            quantity: item.quantity,
                        })),
                        paid_amount: parseFloat(paidAmount),
                        total_amount: total
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Transaksi Berhasil!",
                        html: `
                            <div class="text-left">
                                <p><strong>No. Transaksi: ${data.transaction.transaction_number}</strong></p>
                                <p>Total: ${formatCurrency(data.transaction.total_amount)}</p>
                                <p>Dibayar: ${formatCurrency(data.transaction.paid_amount)}</p>
                                <p>Kembalian: ${formatCurrency(data.transaction.change_amount)}</p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: "Cetak Struk",
                        cancelButtonText: "OK",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(data.receipt_url + '/print', "_blank");
                        }
                    });
                    clearCartWithoutConfirm();
                } else {
                    showAlert("Error: " + (data.message || "Terjadi kesalahan"));
                }
            } catch (error) {
                console.error("Transaction error:", error);
                showAlert("Terjadi kesalahan saat memproses transaksi: " + error.message);
            } finally {
                isProcessingTransaction = false;
                processTransactionButton.innerHTML = `<i class="fas fa-cash-register mr-2"></i> Proses Transaksi`;
                updateTotals();
            }
        }

        /**
         * Clears cart without confirmation (used after successful transaction)
         */
        function clearCartWithoutConfirm() {
            cart = [];
            paidAmount = "";
            paidAmountInput.value = "";
            renderCart();
            updateTotals();
        }

        /**
         * Handle barcode scanning or exact product search
         */
        async function handleBarcodeSearch(barcode) {
            try {
                const response = await fetch(`/api/pos/product-by-barcode?barcode=${encodeURIComponent(barcode)}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success && data.product) {
                    const product = data.product;
                    // Auto-add the first unit to cart
                    if (product.units && product.units.length > 0) {
                        const firstUnit = product.units[0];
                        addToCart(product, firstUnit.unit_id, firstUnit.price, firstUnit.unit_symbol);
                    }
                } else {
                    // If not found by barcode, do regular search
                    handleSearch(barcode);
                }
            } catch (error) {
                console.error("Barcode search error:", error);
                // Fallback to regular search
                handleSearch(barcode);
            }
        }

        // Event Listeners
        document.addEventListener("DOMContentLoaded", () => {
            initializeDate();
            renderCart();
            updateTotals();

            // Search input handling with debounce
            searchInput.addEventListener("input", (e) => {
                searchQuery = e.target.value;
                clearTimeout(searchTimeout);
                
                if (searchQuery.length === 0) {
                    searchResultsDiv.innerHTML = "";
                    searchResultsDiv.classList.add("hidden");
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    // Check if it looks like a barcode (numbers, length > 8)
                    if (/^\d{8,}$/.test(searchQuery)) {
                        handleBarcodeSearch(searchQuery);
                    } else {
                        handleSearch(searchQuery);
                    }
                }, 300);
            });

            // Handle Enter key press on search input
            searchInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && searchQuery) {
                    e.preventDefault();
                    // Clear timeout and search immediately
                    clearTimeout(searchTimeout);
                    if (/^\d{8,}$/.test(searchQuery)) {
                        handleBarcodeSearch(searchQuery);
                    } else {
                        handleSearch(searchQuery);
                    }
                }
            });

            // Paid amount input handling
            paidAmountInput.addEventListener("input", (e) => {
                paidAmount = e.target.value;
                updateTotals();
            });

            // Handle Enter key on paid amount input
            paidAmountInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && !processTransactionButton.disabled) {
                    processTransaction();
                }
            });

            // Quick amount buttons
            document.querySelectorAll(".quick-amount-button").forEach(button => {
                button.addEventListener("click", (e) => {
                    paidAmount = e.target.dataset.amount;
                    paidAmountInput.value = paidAmount;
                    updateTotals();
                });
            });

            // Process transaction button click
            processTransactionButton.addEventListener("click", processTransaction);

            // Clear cart button click
            clearCartButton.addEventListener("click", clearCart);

            // Focus search button click
            focusSearchButton.addEventListener("click", () => {
                searchInput.focus();
            });

            // Custom alert OK button
            customAlertOkButton.addEventListener("click", hideAlert);

            // Close search results when clicking outside
            document.addEventListener("click", (e) => {
                if (!searchInput.contains(e.target) && !searchResultsDiv.contains(e.target)) {
                    searchResultsDiv.classList.add("hidden");
                }
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
                
                // F3 for process transaction (if enabled)
                if (e.key === 'F3' && !processTransactionButton.disabled) {
                    e.preventDefault();
                    processTransaction();
                }
            });
        });
    </script>
</body>
</html>