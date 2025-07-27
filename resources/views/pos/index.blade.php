<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Laravel CSRF Token --}}
    <title>Point of Sale</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: "Inter", sans-serif;
        }
        /* Custom modal styles */
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
    </style>
</head>
<body class="min-h-screen bg-gray-50 p-4">

    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Point of Sale
                    </h1>
                    <p class="text-gray-600">
                        Kasir Toko Kelontong
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Tanggal</p>
                    <p class="text-lg font-semibold" id="current-date">
                        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Product Search & Cart --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Search Product --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        Cari Produk
                    </h2>
                    <div class="relative">
                        <input
                            type="text"
                            id="search-input"
                            placeholder="Scan barcode atau ketik nama produk..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            autofocus
                        />
                        <div class="absolute right-3 top-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>

                    {{-- Search Results --}}
                    <div id="search-results" class="mt-4 border border-gray-200 rounded-lg max-h-60 overflow-y-auto hidden">
                        {{-- Search results will be dynamically inserted here --}}
                    </div>
                </div>

                {{-- Shopping Cart --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">
                            Keranjang Belanja
                        </h2>
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
                        <p class="text-gray-500">
                            Keranjang masih kosong
                        </p>
                    </div>

                    <div id="cart-items" class="space-y-3">
                        {{-- Cart items will be dynamically inserted here --}}
                    </div>
                </div>
            </div>

            {{-- Payment Panel --}}
            <div class="space-y-6">
                {{-- Total Summary --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        Ringkasan
                    </h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">
                                Subtotal:
                            </span>
                            <span id="subtotal-display" class="font-medium">
                                Rp0
                            </span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span id="total-display" class="text-blue-600">
                                    Rp0
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        Pembayaran
                    </h2>
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
                                <span class="text-gray-600">
                                    Kembalian:
                                </span>
                                <span id="change-display" class="font-bold text-lg">
                                    Rp0
                                </span>
                            </div>
                        </div>

                        {{-- Quick Amount Buttons --}}
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

                {{-- Quick Actions --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        Aksi Cepat
                    </h3>
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

    {{-- Custom Alert Modal --}}
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

        // DOM Elements
        const searchInput = document.getElementById("search-input");
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

        /**
         * Displays a custom alert modal instead of the browser's alert.
         * @param {string} message - The message to display.
         */
        function showAlert(message) {
            customAlertMessage.textContent = message;
            customAlertModal.classList.remove("hidden");
        }

        /**
         * Hides the custom alert modal.
         */
        function hideAlert() {
            customAlertModal.classList.add("hidden");
        }

        /**
         * Formats a number as Indonesian Rupiah currency.
         * @param {number} amount - The amount to format.
         * @returns {string} The formatted currency string.
         */
        function formatCurrency(amount) {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0,
            }).format(amount);
        }

        /**
         * Renders the cart items in the DOM.
         */
        function renderCart() {
            if (cart.length === 0) {
                cartEmptyMessage.classList.remove("hidden");
                cartItemsDiv.innerHTML = ""; // Clear existing items
                clearCartButton.classList.add("hidden");
            } else {
                cartEmptyMessage.classList.add("hidden");
                clearCartButton.classList.remove("hidden");
                cartItemsDiv.innerHTML = cart.map((item) => `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">${item.name}</h4>
                            <p class="text-sm text-gray-500">
                                ${formatCurrency(item.price)} per ${item.unit_symbol}
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
         * Updates the subtotal, total, and change displays.
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
         * Handles the product search.
         * @param {string} query - The search query.
         */
        async function handleSearch(query) {
            if (query.length < 2) {
                searchResultsDiv.innerHTML = "";
                searchResultsDiv.classList.add("hidden");
                return;
            }

            try {
                // Mock API call for demonstration. Replace with actual API endpoint.
                // In a real application, this would fetch data from a server.
                const mockProducts = [
                    { id: 1, name: "Sabun Mandi", category: "Perlengkapan Mandi", barcode: "123456789012", units: [{ unit_id: 1, unit_symbol: "pcs", price: 5000 }, { unit_id: 2, unit_symbol: "box", price: 45000 }] },
                    { id: 2, name: "Mie Instan", category: "Makanan", barcode: "987654321098", units: [{ unit_id: 3, unit_symbol: "bungkus", price: 3000 }, { unit_id: 4, unit_symbol: "dus", price: 28000 }] },
                    { id: 3, name: "Kopi Sachet", category: "Minuman", barcode: "112233445566", units: [{ unit_id: 5, unit_symbol: "sachet", price: 1500 }, { unit_id: 6, unit_symbol: "renteng", price: 14000 }] },
                    { id: 4, name: "Gula Pasir 1kg", category: "Bahan Pokok", barcode: "223344556677", units: [{ unit_id: 7, unit_symbol: "kg", price: 13000 }] },
                    { id: 5, name: "Minyak Goreng 2L", category: "Bahan Pokok", barcode: "334455667788", units: [{ unit_id: 8, unit_symbol: "liter", price: 28000 }] },
                ];

                const filteredResults = mockProducts.filter(product =>
                    product.name.toLowerCase().includes(query.toLowerCase()) ||
                    (product.barcode && product.barcode.includes(query))
                );

                if (filteredResults.length > 0) {
                    searchResultsDiv.classList.remove("hidden");
                    searchResultsDiv.innerHTML = filteredResults.map(product => `
                        <div class="p-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">${product.name}</h4>
                                    <p class="text-sm text-gray-500">${product.category}</p>
                                    ${product.barcode ? `<p class="text-xs text-gray-400">Barcode: ${product.barcode}</p>` : ''}
                                </div>
                                <div class="ml-4">
                                    <div class="space-y-1">
                                        ${product.units.map(unit => `
                                            <button
                                                class="block w-full text-right bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded text-sm"
                                                onclick="addToCart(${JSON.stringify(product).replace(/"/g, '&quot;')}, '${unit.unit_id}', ${unit.price}, '${unit.unit_symbol}')"
                                            >
                                                <div class="font-medium">${formatCurrency(unit.price)}</div>
                                                <div class="text-xs text-gray-600">per ${unit.unit_symbol}</div>
                                            </button>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    searchResultsDiv.innerHTML = `<div class="p-3 text-center text-gray-500">Tidak ada produk ditemukan.</div>`;
                    searchResultsDiv.classList.remove("hidden");
                }

            } catch (error) {
                console.error("Search error:", error);
                showAlert("Terjadi kesalahan saat mencari produk.");
                searchResultsDiv.innerHTML = "";
                searchResultsDiv.classList.add("hidden");
            }
        }

        /**
         * Adds a product to the cart.
         * @param {object} product - The product object.
         * @param {string} unitId - The ID of the unit.
         * @param {number} unitPrice - The price of the unit.
         * @param {string} unitSymbol - The symbol of the unit (e.g., 'pcs', 'kg').
         */
        function addToCart(product, unitId, unitPrice, unitSymbol) {
            const existingItemIndex = cart.findIndex(
                (item) => item.product_id === product.id && item.unit_id === unitId
            );

            if (existingItemIndex >= 0) {
                cart[existingItemIndex].quantity += 1;
            } else {
                const newItem = {
                    id: Date.now() + Math.random(), // Unique ID for React key replacement
                    product_id: product.id,
                    unit_id: unitId,
                    name: product.name,
                    unit_symbol: unitSymbol,
                    price: unitPrice,
                    quantity: 1,
                };
                cart.push(newItem);
            }

            searchQuery = "";
            searchInput.value = "";
            searchResultsDiv.innerHTML = "";
            searchResultsDiv.classList.add("hidden");
            searchInput.focus();
            renderCart();
        }

        /**
         * Updates the quantity of an item in the cart.
         * @param {string} itemId - The ID of the item to update.
         * @param {number} newQuantity - The new quantity.
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
         * Removes an item from the cart.
         * @param {string} itemId - The ID of the item to remove.
         */
        function removeFromCart(itemId) {
            cart = cart.filter((item) => item.id != itemId);
            renderCart();
        }

        /**
         * Clears the entire cart.
         */
        function clearCart() {
            cart = [];
            paidAmount = "";
            paidAmountInput.value = "";
            renderCart();
            updateTotals();
        }

        /**
         * Processes the transaction.
         */
        async function processTransaction() {
            if (cart.length === 0) {
                showAlert("Keranjang masih kosong!");
                return;
            }

            const subtotal = cart.reduce((sum, item) => sum + item.quantity * item.price, 0);
            const total = subtotal; // Assuming no tax/discount for simplicity
            if (parseFloat(paidAmount) < total) {
                showAlert("Jumlah pembayaran kurang!");
                return;
            }

            isProcessingTransaction = true;
            processTransactionButton.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...`;
            updateTotals(); // To update button state

            try {
                // Mock API call for demonstration. Replace with actual API endpoint.
                // In a real application, this would send data to a server.
                const response = await fetch("/pos/process", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
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
                        text: `No. Transaksi: ${data.transaction.transaction_number}`,
                        showCancelButton: true,
                        confirmButtonText: "Cetak Struk",
                        cancelButtonText: "OK",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Mock receipt print. In a real app, this would open a new window/tab for printing.
                            window.open(`/pos/receipt/${data.transaction.id}/print`, "_blank");
                        }
                    });
                    clearCart();
                } else {
                    showAlert("Error: " + data.message);
                }
            } catch (error) {
                console.error("Transaction error:", error);
                showAlert("Terjadi kesalahan saat memproses transaksi");
            } finally {
                isProcessingTransaction = false;
                processTransactionButton.innerHTML = `<i class="fas fa-cash-register mr-2"></i> Proses Transaksi`;
                updateTotals(); // To update button state
            }
        }

        // Event Listeners
        document.addEventListener("DOMContentLoaded", () => {
            renderCart(); // Initial render
            updateTotals(); // Initial total update

            // Search input handling with debounce
            searchInput.addEventListener("input", (e) => {
                searchQuery = e.target.value;
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    handleSearch(searchQuery);
                }, 300);
            });

            // Handle Enter key press on search input
            searchInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && searchQuery) {
                    handleSearch(searchQuery);
                    e.preventDefault(); // Prevent form submission if input is part of a form
                }
            });

            // Paid amount input handling
            paidAmountInput.addEventListener("input", (e) => {
                paidAmount = e.target.value;
                updateTotals();
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
        });

        // Mock data for /pos/process endpoint
        // This is a placeholder for your backend logic.
        // In a real Laravel application, this would be handled by a route and controller.
        // This is just to make the frontend 'work' without a full backend.
        if (typeof fetch !== 'undefined') {
            const originalFetch = window.fetch;
            window.fetch = function (url, options) {
                if (url === "/pos/process" && options.method === "POST") {
                    return new Promise((resolve) => {
                        setTimeout(() => {
                            const payload = JSON.parse(options.body);
                            const transactionNumber = `TRX-${Date.now()}`;
                            console.log("Mock transaction processed:", payload);
                            resolve({
                                json: () => Promise.resolve({
                                    success: true,
                                    message: "Transaction successful!",
                                    transaction: {
                                        id: Date.now(),
                                        transaction_number: transactionNumber,
                                        items: payload.items,
                                        paid_amount: payload.paid_amount,
                                        total_amount: payload.total_amount,
                                        change: payload.paid_amount - payload.total_amount,
                                        created_at: new Date().toISOString()
                                    }
                                })
                            });
                        }, 1000); // Simulate network delay
                    });
                }
                 // Mock search endpoint
                if (url.startsWith("/pos/search")) {
                    return new Promise((resolve) => {
                        setTimeout(() => {
                            const query = new URLSearchParams(url.split('?')[1]).get('query');
                            const mockProducts = [
                                { id: 1, name: "Sabun Mandi", category: "Perlengkapan Mandi", barcode: "123456789012", units: [{ unit_id: 1, unit_symbol: "pcs", price: 5000 }, { unit_id: 2, unit_symbol: "box", price: 45000 }] },
                                { id: 2, name: "Mie Instan", category: "Makanan", barcode: "987654321098", units: [{ unit_id: 3, unit_symbol: "bungkus", price: 3000 }, { unit_id: 4, unit_symbol: "dus", price: 28000 }] },
                                { id: 3, name: "Kopi Sachet", category: "Minuman", barcode: "112233445566", units: [{ unit_id: 5, unit_symbol: "sachet", price: 1500 }, { unit_id: 6, unit_symbol: "renteng", price: 14000 }] },
                                { id: 4, name: "Gula Pasir 1kg", category: "Bahan Pokok", barcode: "223344556677", units: [{ unit_id: 7, unit_symbol: "kg", price: 13000 }] },
                                { id: 5, name: "Minyak Goreng 2L", category: "Bahan Pokok", barcode: "334455667788", units: [{ unit_id: 8, unit_symbol: "liter", price: 28000 }] },
                            ];

                            const filteredResults = mockProducts.filter(product =>
                                product.name.toLowerCase().includes(query.toLowerCase()) ||
                                (product.barcode && product.barcode.includes(query))
                            );
                            resolve({
                                json: () => Promise.resolve(filteredResults)
                            });
                        }, 200); // Simulate network delay
                    });
                }
                return originalFetch(url, options);
            };
        }
    </script>
</body>
</html>
