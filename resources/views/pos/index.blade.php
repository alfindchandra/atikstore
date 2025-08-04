<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Point of Sale - Enhanced</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Loading Animation */
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
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Slide animations */
        .slide-in-right {
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Pulse animation for buttons */
        .pulse-success {
            animation: pulseSuccess 0.6s ease-in-out;
        }
        
        @keyframes pulseSuccess {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Modal styles */
        .modal-backdrop {
            backdrop-filter: blur(4px);
        }
        
        /* Quick pay button hover effects */
        .quick-pay-btn {
            transition: all 0.2s ease-in-out;
        }
        
        .quick-pay-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Digital clock */
        .digital-clock {
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.1em;
        }
        
        /* Cart item animations */
        .cart-item {
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="max-w-7xl mx-auto p-4 ">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-blue-100 hidden lg:block">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-3 rounded-lg">
                        <i class="fas fa-cash-register text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Point of Sale</h1>
                        <p class="text-gray-600">Sistem Kasir Modern</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-lg">
                        <p class="text-sm opacity-90">Jakarta, Indonesia</p>
                        <p class="text-lg font-bold digital-clock" id="current-time"></p>
                        <p class="text-sm opacity-90" id="current-date"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Left Panel - Product Search & Cart -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Product Search -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-search text-blue-600 mr-2"></i>
                            Cari Produk
                        </h2>
                        <button id="add-product-btn" class="bg-green-600 hover:bg-green-700 text-white px-2 lg:px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Produk
                        </button>
                    </div>
                    
                    <div class="relative">
                        <input
                            type="text"
                            id="search-input"
                            placeholder="Scan barcode atau ketik nama produk..."
                            class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                            autofocus
                        />
                        <div class="absolute left-4 top-3.5">
                            <i id="search-icon" class="fas fa-search text-gray-400"></i>
                        </div>
                        <div class="absolute right-3 top-2">
                            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">F1</span>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div id="search-results" class="mt-4 border border-gray-200 rounded-lg max-h-80 overflow-y-auto custom-scrollbar hidden">
                        <!-- Search results will be dynamically inserted here -->
                    </div>
                </div>

                <!-- Shopping Cart -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-shopping-cart text-blue-600 mr-2"></i>
                            Keranjang Belanja
                            <span id="cart-count" class="ml-2 bg-blue-600 text-white text-sm px-2 py-1 rounded-full hidden">0</span>
                        </h2>
                        <button
                            id="clear-cart-button"
                            class="text-red-600 hover:text-red-700 px-3 py-2 rounded-lg hover:bg-red-50 transition-colors hidden"
                        >
                            <i class="fas fa-trash mr-1"></i>
                            Kosongkan
                        </button>
                    </div>

                    <div id="cart-empty-message" class="text-center py-12">
                        <div class="bg-gray-50 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-cart text-gray-400 text-3xl"></i>
                        </div>
                        <p class="text-gray-500 text-lg">Keranjang masih kosong</p>
                        <p class="text-gray-400 text-sm mt-1">Scan atau cari produk untuk memulai</p>
                    </div>

                    <div id="cart-items" class="space-y-4">
                        <!-- Cart items will be dynamically inserted here -->
                    </div>
                </div>
            </div>

            <!-- Right Panel - Payment & Summary -->
            <div class="space-y-6">
                <!-- Total Summary -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
                    <h2 class="text-xl font-semibold mb-6 text-gray-900 flex items-center">
                        <i class="fas fa-calculator text-blue-600 mr-2"></i>
                        Ringkasan
                    </h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Subtotal:</span>
                            <span id="subtotal-display" class="font-bold text-lg">Rp0</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg">
                            <span class="text-lg">Total:</span>
                            <span id="total-display" class="font-bold text-2xl">Rp0</span>
                        </div>
                    </div>
                </div>

                <!-- Payment -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
                    <h2 class="text-xl font-semibold mb-6 text-gray-900 flex items-center">
                        <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                        Pembayaran
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="paid-amount-input" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah Dibayar
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    id="paid-amount-input"
                                    placeholder="0"
                                    class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold"
                                />
                                <div class="absolute right-3 top-2">
                                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">F2</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Amount Buttons -->
                        <div class="grid grid-cols-2 gap-2">
                            <button class="quick-pay-btn quick-amount-button px-3 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg font-medium text-sm transition-all" data-amount="10000">
                                Rp10.000
                            </button>
                            <button class="quick-pay-btn quick-amount-button px-3 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg font-medium text-sm transition-all" data-amount="20000">
                                Rp20.000
                            </button>
                            <button class="quick-pay-btn quick-amount-button px-3 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium text-sm transition-all" data-amount="50000">
                                Rp50.000
                            </button>
                            <button class="quick-pay-btn quick-amount-button px-3 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium text-sm transition-all" data-amount="100000">
                                Rp100.000
                            </button>
                            
                        </div>

                        <!-- Exact Amount Button -->
                        <button id="exact-amount-btn" class="w-full py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg font-medium transition-all quick-pay-btn">
                            <i class="fas fa-equals mr-2"></i>
                            Uang Pas
                        </button>

                        <div id="change-display-container" class="p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg hidden">
                            <div class="flex justify-between items-center">
                                <span class="text-green-800 font-medium">Kembalian:</span>
                                <span id="change-display" class="font-bold text-xl text-green-800">Rp0</span>
                            </div>
                        </div>

                        <button
                            id="process-transaction-button"
                            class="w-full py-4 rounded-lg font-semibold text-lg bg-gray-300 text-gray-500 cursor-not-allowed transition-all duration-300"
                            disabled
                        >
                            <i class="fas fa-cash-register mr-2"></i>
                            Proses Transaksi
                        </button>
                        
                        <div class="text-center text-xs text-gray-500">
                            Tekan F3 untuk proses transaksi
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 flex items-center">
                        <i class="fas fa-bolt text-blue-600 mr-2"></i>
                        Aksi Cepat
                    </h3>
                    <div class="grid grid-cols-1 gap-3">
                        <button
                            id="focus-search-button"
                            class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg font-medium transition-all quick-pay-btn"
                        >
                            <i class="fas fa-search mr-2"></i>
                            Fokus Pencarian (F1)
                        </button>
                        <button
                            onclick="window.location.href = '/products'"
                            class="w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium transition-all quick-pay-btn"
                        >
                            <i class="fas fa-box mr-2"></i>
                            Kelola Produk
                        </button>
                        <button
                            onclick="window.location.href = '/stock'"
                            class="w-full px-4 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white rounded-lg font-medium transition-all quick-pay-btn"
                        >
                            <i class="fas fa-warehouse mr-2"></i>
                            Kelola Stok
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="add-product-modal" class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Tambah Produk Baru</h3>
                    <button id="close-add-product-modal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="add-product-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" id="new-product-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Barcode (Opsional)</label>
                        <input type="text" id="new-product-barcode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                        <input type="number" id="new-product-price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" step="100" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                        <input type="text" id="new-product-unit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="pcs, kg, liter, dll" value="pcs">
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="button" id="cancel-add-product" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div id="edit-item-modal" class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Edit Item</h3>
                    <button id="close-edit-modal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="edit-item-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" id="edit-item-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" readonly>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Satuan</label>
                        <input type="number" id="edit-item-price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" step="100">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <input type="number" id="edit-item-quantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0.1" step="0.1">
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="button" id="cancel-edit" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
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
        let editingItemId = null;

        // DOM Elements
        const searchInput = document.getElementById("search-input");
        const searchIcon = document.getElementById("search-icon");
        const searchResultsDiv = document.getElementById("search-results");
        const cartItemsDiv = document.getElementById("cart-items");
        const cartEmptyMessage = document.getElementById("cart-empty-message");
        const cartCount = document.getElementById("cart-count");
        const clearCartButton = document.getElementById("clear-cart-button");
        const subtotalDisplay = document.getElementById("subtotal-display");
        const totalDisplay = document.getElementById("total-display");
        const paidAmountInput = document.getElementById("paid-amount-input");
        const changeDisplayContainer = document.getElementById("change-display-container");
        const changeDisplay = document.getElementById("change-display");
        const processTransactionButton = document.getElementById("process-transaction-button");
        const focusSearchButton = document.getElementById("focus-search-button");
        const exactAmountBtn = document.getElementById("exact-amount-btn");
        const addProductBtn = document.getElementById("add-product-btn");
        const addProductModal = document.getElementById("add-product-modal");
        const editItemModal = document.getElementById("edit-item-modal");

        /**
         * Initialize date and time display
         */
        function initializeDateTime() {
            function updateTime() {
                const now = new Date();
                const timeOptions = {
                    timeZone: 'Asia/Jakarta',
                    hour12: false,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };
                const dateOptions = {
                    timeZone: 'Asia/Jakarta',
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                
                document.getElementById("current-time").textContent = now.toLocaleTimeString('id-ID', timeOptions);
                document.getElementById("current-date").textContent = now.toLocaleDateString('id-ID', dateOptions);
            }
            
            updateTime();
            setInterval(updateTime, 1000);
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
                cartCount.classList.add("hidden");
            } else {
                cartEmptyMessage.classList.add("hidden");
                clearCartButton.classList.remove("hidden");
                cartCount.classList.remove("hidden");
                cartCount.textContent = cart.length;
                
                cartItemsDiv.innerHTML = cart.map((item, index) => `
                    <div class="cart-item bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 text-lg">${escapeHtml(item.name)}</h4>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-sm text-blue-600 font-medium">${formatCurrency(item.price)}</span>
                                    <span class="text-sm text-gray-400">per ${escapeHtml(item.unit_symbol)}</span>
                                </div>
                            </div>
                            <button onclick="editCartItem('${item.id}')" class="text-blue-600 hover:text-blue-700 p-2 hover:bg-blue-50 rounded-lg transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-between mt-4">
                            <div class="flex items-center space-x-3">
                                <button
                                    class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-colors"
                                    onclick="updateQuantity('${item.id}', ${item.quantity - 1})"
                                >
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <div class="bg-white border border-gray-300 rounded-lg px-3 py-2 min-w-[80px] text-center">
                                    <span class="font-semibold text-lg">${item.quantity}</span>
                                </div>
                                <button
                                    class="w-8 h-8 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center transition-colors"
                                    onclick="updateQuantity('${item.id}', ${item.quantity + 1})"
                                >
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            
                            <div class="text-right">
                                <div class="text-xl font-bold text-gray-900">
                                    ${formatCurrency(item.quantity * item.price)}
                                </div>
                                <button
                                    class="text-red-600 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-colors mt-1"
                                    onclick="removeFromCart('${item.id}')"
                                >
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
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

            // Update exact amount button
            if (total > 0) {
                exactAmountBtn.innerHTML = `<i class="fas fa-equals mr-2"></i>Uang Pas (${formatCurrency(total)})`;
                exactAmountBtn.disabled = false;
                exactAmountBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                exactAmountBtn.innerHTML = `<i class="fas fa-equals mr-2"></i>Uang Pas`;
                exactAmountBtn.disabled = true;
                exactAmountBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }

            if (paidAmount && parseFloat(paidAmount) >= total) {
                changeDisplayContainer.classList.remove("hidden");
                changeDisplay.textContent = formatCurrency(change);
                changeDisplay.className = `font-bold text-xl ${change >= 0 ? "text-green-800" : "text-red-800"}`;
            } else {
                changeDisplayContainer.classList.add("hidden");
            }

            // Update process transaction button state
            if (cart.length === 0 || parseFloat(paidAmount || 0) < total || isProcessingTransaction) {
                processTransactionButton.disabled = true;
                processTransactionButton.classList.remove("bg-gradient-to-r", "from-blue-600", "to-indigo-600", "hover:from-blue-700", "hover:to-indigo-700", "text-white");
                processTransactionButton.classList.add("bg-gray-300", "text-gray-500", "cursor-not-allowed");
            } else {
                processTransactionButton.disabled = false;
                processTransactionButton.classList.remove("bg-gray-300", "text-gray-500", "cursor-not-allowed");
                processTransactionButton.classList.add("bg-gradient-to-r", "from-blue-600", "to-indigo-600", "hover:from-blue-700", "hover:to-indigo-700", "text-white");
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
                
                if (data.error) {
                    throw new Error(data.message || 'Terjadi kesalahan saat mencari produk');
                }

                const filteredResults = Array.isArray(data) ? data : [];

                if (filteredResults.length > 0) {
                    searchResultsDiv.classList.remove("hidden");
                    searchResultsDiv.innerHTML = filteredResults.map(product => `
                        <div class="p-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 text-lg">${escapeHtml(product.name)}</h4>
                                    <p class="text-sm text-blue-600 font-medium">${escapeHtml(product.category)}</p>
                                    ${product.barcode ? `<p class="text-xs text-gray-500 mt-1">Barcode: ${escapeHtml(product.barcode)}</p>` : ''}
                                    ${product.stock_info && product.stock_info.length > 0 ?
                                        `<p class="text-xs text-gray-600 mt-1">
                                            <i class="fas fa-box mr-1"></i>
                                            Stok: ${product.stock_info.map(s => `${s.quantity} ${escapeHtml(s.unit_symbol)}`).join(', ')}
                                        </p>` :
                                        `<p class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-box mr-1"></i>
                                            Stok: N/A
                                        </p>`
                                    }
                                </div>
                                <div class="ml-4">
                                    <div class="space-y-2">
                                        ${product.units.map(unit => `
                                            <button
                                                class="block w-full text-right bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg transition-all transform hover:scale-105"
                                                onclick="addToCart(${escapeHtml(JSON.stringify(product))}, '${unit.unit_id}', ${unit.price}, '${escapeHtml(unit.unit_symbol)}')"
                                            >
                                                <div class="font-bold">${formatCurrency(unit.price)}</div>
                                                <div class="text-xs opacity-90">per ${escapeHtml(unit.unit_symbol)}</div>
                                            </button>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    searchResultsDiv.innerHTML = `
                        <div class="p-4 text-center text-gray-500">
                            <i class="fas fa-search text-2xl mb-2"></i>
                            <p>Tidak ada produk ditemukan untuk "${escapeHtml(query)}"</p>
                        </div>
                    `;
                    searchResultsDiv.classList.remove("hidden");
                }

            } catch (error) {
                console.error("Search error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "Terjadi kesalahan saat mencari produk: " + error.message,
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
                searchResultsDiv.innerHTML = `
                    <div class="p-4 text-center text-red-500">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p>Terjadi kesalahan saat mencari produk</p>
                    </div>
                `;
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
                        id: Date.now() + Math.random(),
                        product_id: product.id,
                        unit_id: unitId,
                        name: product.name,
                        unit_symbol: unitSymbol,
                        price: parseFloat(unitPrice),
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
                
                // Success animation
                const button = event.target.closest('button');
                if (button) {
                    button.classList.add('pulse-success');
                    setTimeout(() => button.classList.remove('pulse-success'), 600);
                }
                
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "Terjadi kesalahan saat menambahkan produk ke keranjang",
                    toast: true,
                    position: 'top-end',
                    timer: 3000
                });
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
         * Edit cart item
         */
        function editCartItem(itemId) {
            const item = cart.find(item => item.id == itemId);
            if (!item) return;

            editingItemId = itemId;
            document.getElementById('edit-item-name').value = item.name;
            document.getElementById('edit-item-price').value = item.price;
            document.getElementById('edit-item-quantity').value = item.quantity;
            editItemModal.classList.remove('hidden');
        }

        /**
         * Processes the transaction
         */
        async function processTransaction() {
            if (cart.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keranjang Kosong!',
                    text: 'Tambahkan produk ke keranjang terlebih dahulu',
                    toast: true,
                    position: 'top-end',
                    timer: 3000
                });
                return;
            }

            const subtotal = cart.reduce((sum, item) => sum + item.quantity * item.price, 0);
            const total = subtotal;
            
            if (parseFloat(paidAmount) < total) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pembayaran Kurang!',
                    text: 'Jumlah pembayaran kurang dari total',
                    toast: true,
                    position: 'top-end',
                    timer: 3000
                });
                paidAmountInput.focus();
                return;
            }

            const result = await Swal.fire({
                title: 'Konfirmasi Transaksi',
                html: `
                    <div class="text-left bg-gray-50 p-4 rounded-lg">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium">Total:</span>
                                <span class="font-bold text-blue-600">${formatCurrency(total)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Dibayar:</span>
                                <span>${formatCurrency(parseFloat(paidAmount))}</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span>Kembalian:</span>
                                <span class="font-bold text-green-600">${formatCurrency(parseFloat(paidAmount) - total)}</span>
                            </div>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fas fa-cash-register mr-2"></i>Proses Transaksi',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-xl'
                }
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
                            <div class="text-left bg-green-50 p-4 rounded-lg">
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="font-medium">No. Transaksi:</span>
                                        <span class="font-bold">${data.transaction.transaction_number}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Total:</span>
                                        <span>${formatCurrency(data.transaction.total_amount)}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Dibayar:</span>
                                        <span>${formatCurrency(data.transaction.paid_amount)}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2">
                                        <span>Kembalian:</span>
                                        <span class="font-bold text-green-600">${formatCurrency(data.transaction.change_amount)}</span>
                                    </div>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-print mr-2"></i>Cetak Struk',
                        cancelButtonText: 'OK',
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(data.receipt_url + '/print', "_blank");
                        }
                    });
                    clearCartWithoutConfirm();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Transaksi Gagal!',
                        text: data.message || "Terjadi kesalahan",
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });
                }
            } catch (error) {
                console.error("Transaction error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "Terjadi kesalahan saat memproses transaksi: " + error.message,
                    customClass: {
                        popup: 'rounded-xl'
                    }
                });
            } finally {
                isProcessingTransaction = false;
                processTransactionButton.innerHTML = `<i class="fas fa-cash-register mr-2"></i> Proses Transaksi`;
                updateTotals();
            }
        }

        /**
         * Clears cart without confirmation
         */
        function clearCartWithoutConfirm() {
            cart = [];
            paidAmount = "";
            paidAmountInput.value = "";
            renderCart();
            updateTotals();
        }

        /**
         * Handle barcode scanning
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
                    if (product.units && product.units.length > 0) {
                        const firstUnit = product.units[0];
                        addToCart(product, firstUnit.unit_id, firstUnit.price, firstUnit.unit_symbol);
                    }
                } else {
                    handleSearch(barcode);
                }
            } catch (error) {
                console.error("Barcode search error:", error);
                handleSearch(barcode);
            }
        }

        /**
         * Add new product (simplified version for POS)
         */
        async function addNewProduct() {
            const name = document.getElementById('new-product-name').value.trim();
            const barcode = document.getElementById('new-product-barcode').value.trim();
            const price = parseFloat(document.getElementById('new-product-price').value);
            const unit = document.getElementById('new-product-unit').value.trim() || 'pcs';

            if (!name || !price || price <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap!',
                    text: 'Mohon isi nama produk dan harga dengan benar',
                    toast: true,
                    position: 'top-end',
                    timer: 3000
                });
                return;
            }

            // For demo purposes, we'll add it directly to cart as a temporary product
            const tempProduct = {
                id: 'temp_' + Date.now(),
                name: name,
                barcode: barcode,
                category: 'Produk Baru'
            };

            const newItem = {
                id: Date.now() + Math.random(),
                product_id: tempProduct.id,
                unit_id: 'temp_unit',
                name: tempProduct.name,
                unit_symbol: unit,
                price: price,
                quantity: 1,
            };

            cart.push(newItem);
            renderCart();
            
            // Clear form and close modal
            document.getElementById('add-product-form').reset();
            document.getElementById('new-product-unit').value = 'pcs';
            addProductModal.classList.add('hidden');
            
            Swal.fire({
                icon: 'success',
                title: 'Produk Ditambahkan!',
                text: `${name} berhasil ditambahkan ke keranjang`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // Event Listeners
        document.addEventListener("DOMContentLoaded", () => {
            initializeDateTime();
            renderCart();
            updateTotals();

            // Search input handling
            searchInput.addEventListener("input", (e) => {
                searchQuery = e.target.value;
                clearTimeout(searchTimeout);
                
                if (searchQuery.length === 0) {
                    searchResultsDiv.innerHTML = "";
                    searchResultsDiv.classList.add("hidden");
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    if (/^\d{8,}$/.test(searchQuery)) {
                        handleBarcodeSearch(searchQuery);
                    } else {
                        handleSearch(searchQuery);
                    }
                }, 300);
            });

            searchInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && searchQuery) {
                    e.preventDefault();
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
                    e.target.classList.add('pulse-success');
                    setTimeout(() => e.target.classList.remove('pulse-success'), 600);
                });
            });

            // Exact amount button
            exactAmountBtn.addEventListener("click", () => {
                const total = cart.reduce((sum, item) => sum + item.quantity * item.price, 0);
                if (total > 0) {
                    paidAmount = total.toString();
                    paidAmountInput.value = paidAmount;
                    updateTotals();
                    exactAmountBtn.classList.add('pulse-success');
                    setTimeout(() => exactAmountBtn.classList.remove('pulse-success'), 600);
                }
            });

            // Button event listeners
            processTransactionButton.addEventListener("click", processTransaction);
            clearCartButton.addEventListener("click", clearCart);
            focusSearchButton.addEventListener("click", () => {
                searchInput.focus();
            });

            // Modal event listeners
            addProductBtn.addEventListener("click", () => {
                addProductModal.classList.remove('hidden');
                document.getElementById('new-product-name').focus();
            });

            document.getElementById('close-add-product-modal').addEventListener("click", () => {
                addProductModal.classList.add('hidden');
            });

            document.getElementById('cancel-add-product').addEventListener("click", () => {
                addProductModal.classList.add('hidden');
            });

            document.getElementById('add-product-form').addEventListener("submit", (e) => {
                e.preventDefault();
                addNewProduct();
            });

            document.getElementById('close-edit-modal').addEventListener("click", () => {
                editItemModal.classList.add('hidden');
            });

            document.getElementById('cancel-edit').addEventListener("click", () => {
                editItemModal.classList.add('hidden');
            });

            document.getElementById('edit-item-form').addEventListener("submit", (e) => {
                e.preventDefault();
                const newPrice = parseFloat(document.getElementById('edit-item-price').value);
                const newQuantity = parseFloat(document.getElementById('edit-item-quantity').value);
                
                if (newPrice > 0 && newQuantity > 0) {
                    cart = cart.map(item => {
                        if (item.id == editingItemId) {
                            return { ...item, price: newPrice, quantity: newQuantity };
                        }
                        return item;
                    });
                    renderCart();
                    editItemModal.classList.add('hidden');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Item Diperbarui!',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });

            // Close modals when clicking outside
            addProductModal.addEventListener("click", (e) => {
                if (e.target === addProductModal) {
                    addProductModal.classList.add('hidden');
                }
            });

            editItemModal.addEventListener("click", (e) => {
                if (e.target === editItemModal) {
                    editItemModal.classList.add('hidden');
                }
            });

            // Close search results when clicking outside
            document.addEventListener("click", (e) => {
                if (!searchInput.contains(e.target) && !searchResultsDiv.contains(e.target)) {
                    searchResultsDiv.classList.add("hidden");
                }
            });

            // Keyboard shortcuts
            document.addEventListener("keydown", (e) => {
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
                
                // F3 for process transaction
                if (e.key === 'F3' && !processTransactionButton.disabled) {
                    e.preventDefault();
                    processTransaction();
                }
                
                // Escape to close modals
                if (e.key === 'Escape') {
                    addProductModal.classList.add('hidden');
                    editItemModal.classList.add('hidden');
                    searchResultsDiv.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>