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
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

        /* Price edit popup */
        .price-edit-popup {
            position: absolute;
            z-index: 1000;
            background: white;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            padding: 12px;
            min-width: 200px;
        }

        .price-highlight {
            background: linear-gradient(45deg, #fbbf24, #f59e0b);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .price-highlight:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.4);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100" 
      x-data="posSystem()" 
      x-init="initializePOS()">
    
    <div class="max-w-7xl mx-auto p-4">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Left Panel - Product Search & Cart -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Product Search -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
                    
                    
                    <div class="relative">
                        <input
                            type="text"
                            x-model="searchQuery"
                            @input.debounce.300ms="handleSearch"
                            @keydown.enter="handleSearch"
                            placeholder="Scan barcode atau ketik nama produk..."
                            class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                            autofocus
                        />
                        <div class="absolute left-4 top-3.5">
                            <i class="fas fa-search text-gray-400" x-show="!isSearching"></i>
                            <div class="loading-spinner" x-show="isSearching"></div>
                        </div>
                        
                    </div>

                    <!-- Search Results -->
<div x-show="searchResults.length > 0 || (searchQuery.length >= 2 && !isSearching)" 
     class="mt-4 border border-gray-200 rounded-lg max-h-80 overflow-y-auto custom-scrollbar">
    <template x-for="product in searchResults" :key="product.id">
        <div class="search-result-enhanced p-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 text-lg" x-text="product.name"></h4>
                    <p class="text-xs text-gray-500 mt-1 hidden md:block" x-show="product.barcode" x-text="'Barcode: ' + product.barcode"></p>
                    <p class="text-xs text-gray-600 mt-1">
                        <i class="fas fa-box mr-1"></i>
                        <span x-text="product.stock_info?.length > 0 ? product.stock_info.map(s => s.quantity + ' ' + s.unit_symbol).join(', ') : 'Stok: N/A'"></span>
                    </p>
                </div>
                <div class="ml-4">
                    <div class="space-y-2">
                        <template x-for="unit in product.units" :key="unit.unit_id">
                            <div class="text-right">
                                <button
                                    @click="addToCart(product, unit.unit_id, unit.price, unit.unit_symbol)"
                                    class="block w-full text-right bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg transition-all transform hover:scale-105">
                                    <div class="font-bold" x-text="formatCurrency(unit.price)+'/'+unit.unit_symbol"></div>
                                    
                                    <!-- Show tiered pricing indicator -->
                                    <div x-show="unit.enable_tiered_pricing && unit.tiered_prices?.length > 0" 
                                         class="text-xs opacity-90 mt-1">
                                        <i class="fas fa-layer-group mr-1"></i>
                                        <span x-text="unit.tiered_prices.length + ' tier harga'"></span>
                                    </div>
                                    
                                    <!-- Show purchase limits -->
                                    <div class="text-xs opacity-90 mt-1">
                                        <span x-text="'Min: ' + (unit.min_purchase || 1)"></span>
                                        <span x-show="unit.max_purchase" x-text="' • Max: ' + unit.max_purchase"></span>
                                    </div>
                                </button>
                                
                                <!-- Tiered pricing preview -->
                                <div x-show="unit.enable_tiered_pricing && unit.tiered_prices?.length > 0" 
                                     class="mt-2 bg-orange-50 border border-orange-200 rounded-lg p-2 tier-price-preview">
                                    <div class="text-xs text-orange-800 font-medium mb-1">
                                        <i class="fas fa-info-circle mr-1"></i>Harga Bertingkat:
                                    </div>
                                    <template x-for="tier in unit.tiered_prices.slice(0, 3)" :key="tier.min_quantity">
                                        <div class="text-xs text-orange-700 flex justify-between">
                                            <span x-text="'≥' + tier.min_quantity + ' unit:'"></span>
                                            <span x-text="formatCurrency(tier.price)"></span>
                                        </div>
                                    </template>
                                    <div x-show="unit.tiered_prices.length > 3" 
                                         class="text-xs text-orange-600 text-center mt-1">
                                        <span x-text="'dan ' + (unit.tiered_prices.length - 3) + ' tier lainnya...'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <div x-show="searchQuery.length >= 2 && searchResults.length === 0 && !isSearching" 
         class="p-4 text-center text-gray-500">
        <i class="fas fa-search text-2xl mb-2"></i>
        <p x-text="'Tidak ada produk ditemukan untuk \"' + searchQuery + '\"'"></p>
    </div>
</div>
                </div>

                <!-- Shopping Cart -->
                <!-- Shopping Cart -->
<div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-sm md:text-lg font-semibold text-gray-900 flex items-center">
            <i class="fas fa-shopping-cart text-blue-600 mr-2"></i>
            Keranjang Belanja
            <span x-show="cart.length > 0" 
                  class="ml-1 md:ml-2 bg-blue-600 text-white text-sm px-2 py-1 rounded-full" 
                  x-text="cart.length"></span>
        </h2>
        <button
            x-show="cart.length > 0"
            @click="clearCart()"
            class="text-red-600 hover:text-red-700 px-3 py-2 rounded-lg hover:bg-red-50 transition-colors">
            <i class="fas fa-trash mr-1"></i>
            Kosongkan
        </button>
    </div>

    <div x-show="cart.length === 0" class="text-center py-12">
        <div class="bg-gray-50 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-shopping-cart text-gray-400 text-3xl"></i>
        </div>
        <p class="text-gray-500 text-lg">Keranjang masih kosong</p>
        <p class="text-gray-400 text-sm mt-1">Scan atau cari produk untuk memulai</p>
    </div>

    <!-- ENHANCED CART CONTAINER - Ganti bagian ini -->
    <div class="space-y-4" id="enhanced-cart-container">
        <template x-for="(item, index) in cart" :key="item.id">
            <div class="cart-item-enhanced p-4 bg-white rounded-xl shadow-sm border border-gray-200 mb-3 transition-all"
                 :class="{ 'has-discount': item.discount_amount > 0 }">

                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <!-- Product Info -->
                    <div class="flex-1 min-w-0 pr-4 mb-2 sm:mb-0">
                        <div class="flex items-center space-x-2">
                            <h4 class="font-semibold text-gray-900 text-base md:text-lg truncate" x-text="item.name"></h4>
                            
                            <!-- Tier indicator -->
                            <div x-show="item.applied_tier" 
                                 class="tier-badge tier-indicator cursor-pointer"
                                 @click="selectedTieredItem = item; showTieredPricingModal = true"
                                 title="Klik untuk detail tier">
                                <i class="fas fa-layer-group mr-1"></i>
                                <span x-text="'T' + item.applied_tier?.min_quantity"></span>
                            </div>
                            
                            <!-- Savings indicator -->
                            <div x-show="item.discount_amount > 0" class="savings-badge">
                                <i class="fas fa-piggy-bank mr-1"></i>
                                <span x-text="formatCurrency(item.discount_amount)"></span>
                            </div>
                        </div>
                        
                        <!-- Price information -->
                        <div class="flex items-center space-x-2 text-sm mt-1">
                            <div class="flex items-center space-x-1">
                                <span class="text-gray-600" x-text="formatCurrency(item.price)"></span>
                                <span class="text-xs text-gray-500" x-text="'/ ' + item.unit_symbol"></span>
                            </div>
                            
                            <!-- Original price if discounted -->
                            <div x-show="item.discount_amount > 0" class="flex items-center space-x-1">
                                <span class="text-xs line-through text-gray-400" x-text="formatCurrency(item.base_price)"></span>
                            </div>
                        </div>

                        <!-- Purchase limits -->
                        <div class="purchase-limit-indicator mt-1">
                            <i class="fas fa-sliders-h mr-1"></i>
                            <span x-text="'Min: ' + (item.min_purchase || 1)"></span>
                            <span class="mx-1">•</span>
                            <span x-text="'Max: ' + (item.max_purchase || 'Unlimited')"></span>
                        </div>
                    </div>

                    <!-- Quantity and Price Controls -->
                    <div class="flex items-center justify-between w-full sm:w-auto space-x-4">
                        <!-- Quantity controls -->
                        <div class="quantity-stepper flex items-center space-x-2 px-2 py-1"
                             :class="{ 'has-limits': item.min_purchase > 1 || item.max_purchase }">
                            <button
                                @click="updateQuantity(item.id, item.quantity - 1)"
                                :disabled="item.quantity <= (item.min_purchase || 1)"
                                :class="item.quantity <= (item.min_purchase || 1) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-100'"
                                class="w-7 h-7 bg-red-50 text-red-600 rounded-full flex items-center justify-center transition-colors">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <div class="text-sm font-semibold w-8 text-center" x-text="item.quantity"></div>
                            <button
                                @click="updateQuantity(item.id, item.quantity + 1)"
                                :disabled="item.max_purchase && item.quantity >= item.max_purchase"
                                :class="(item.max_purchase && item.quantity >= item.max_purchase) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-100'"
                                class="w-7 h-7 bg-green-50 text-green-600 rounded-full flex items-center justify-center transition-colors">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                        
                        <!-- Total and actions -->
                        <div class="flex items-center space-x-2 ml-auto sm:ml-4">
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900" 
                                     x-text="formatCurrency(item.quantity * item.price)"></div>
                                <!-- Show per-item savings -->
                                <div x-show="item.discount_amount > 0" 
                                     class="text-xs text-green-600 font-medium">
                                    <span x-text="'Hemat: ' + formatCurrency(item.discount_amount)"></span>
                                </div>
                            </div>
                            <button
                                @click="removeFromCart(item.id)"
                                class="text-gray-400 hover:text-red-500 p-2 rounded-full transition-colors">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
            </div>

            <!-- Right Panel - Payment & Summary -->
            <div class="space-y-6">
                <!-- Total Summary -->
                <!-- Total Summary -->
<div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
    <h2 class="text-xl font-semibold mb-6 text-gray-900 flex items-center">
        <i class="fas fa-calculator text-blue-600 mr-2"></i>
        Ringkasan
    </h2>
    <div class="space-y-4">
        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
            <span class="text-gray-600">Subtotal:</span>
            <span class="font-bold text-lg" x-text="formatCurrency(subtotal)"></span>
        </div>
        
        <!-- Show total savings from tiered pricing -->
        <div x-show="totalSavings > 0" class="flex justify-between items-center p-3 bg-green-50 rounded-lg border border-green-200">
            <div class="flex items-center space-x-2">
                <i class="fas fa-piggy-bank text-green-600"></i>
                <span class="text-green-800 font-medium">Total Hemat:</span>
            </div>
            <span class="font-bold text-lg text-green-800" x-text="formatCurrency(totalSavings)"></span>
        </div>
        
        <div x-show="additionalCosts.length > 0" class="space-y-2">
            <template x-for="cost in additionalCosts" :key="cost.id">
                <div class="flex justify-between items-center p-2 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="flex-1">
                        <span class="text-sm text-yellow-800" x-text="cost.description"></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-medium text-yellow-800" x-text="formatCurrency(cost.amount)"></span>
                        <button @click="removeAdditionalCost(cost.id)" 
                                class="text-red-600 hover:text-red-700 text-xs">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
        
        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg">
            <span class="text-lg">Total:</span>
            <span class="font-bold text-2xl" x-text="formatCurrency(totalAmount)"></span>
        </div>
        
        <!-- Add Additional Cost Button -->
        <button @click="showAdditionalCostModal = true" 
                class="w-full py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white rounded-lg font-medium transition-all">
            <i class="fas fa-plus mr-2"></i>
            Tambah Biaya Lain
        </button>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah Dibayar
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    x-model.number="paidAmount"
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
                            <template x-for="amount in quickAmounts" :key="amount">
                                <button @click="setQuickAmount(amount)" 
                                        class="quick-pay-btn px-3 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg font-medium text-sm transition-all" 
                                        x-text="formatCurrency(amount)">
                                </button>
                            </template>
                        </div>

                        <!-- Exact Amount Button -->
                        <button @click="setExactAmount()" 
                                :disabled="totalAmount <= 0"
                                :class="totalAmount > 0 ? 'bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700' : 'bg-gray-300 cursor-not-allowed'"
                                class="w-full py-3 text-white rounded-lg font-medium transition-all quick-pay-btn">
                            <i class="fas fa-equals mr-2"></i>
                            <span x-text="totalAmount > 0 ? 'Uang Pas (' + formatCurrency(totalAmount) + ')' : 'Uang Pas'"></span>
                        </button>

                        <div x-show="changeAmount > 0" 
                             class="p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-green-800 font-medium">Kembalian:</span>
                                <span class="font-bold text-xl text-green-800" x-text="formatCurrency(changeAmount)"></span>
                            </div>
                        </div>

                        <button
                            @click="processTransaction()"
                            :disabled="!canProcessTransaction"
                            :class="canProcessTransaction ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                            class="w-full py-4 rounded-lg font-semibold text-lg transition-all duration-300"
                        >
                            <i class="fas fa-cash-register mr-2"></i>
                            <span x-show="!isProcessingTransaction">Proses Transaksi</span>
                            <span x-show="isProcessingTransaction">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Memproses...
                            </span>
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
                        <button @click="window.location.href = '/'" 
                                class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg font-medium transition-all quick-pay-btn">
                            <i class="fas fa-home mr-2"></i>
                           Kembali Ke Home
                        </button>
                        <button onclick="window.location.href = '/products/create'" 
                                class="w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium transition-all quick-pay-btn">
                            <i class="fas fa-box mr-2"></i>
                            Kelola Produk
                        </button>
                        <button onclick="window.location.href = '/stock'" 
                                class="w-full px-4 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white rounded-lg font-medium transition-all quick-pay-btn">
                            <i class="fas fa-warehouse mr-2"></i>
                            Kelola Stok
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div x-show="showAddProductModal" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop z-50 flex items-center justify-center p-4"
         @click.self="showAddProductModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto"
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Tambah Produk Baru</h3>
                    <button @click="showAddProductModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form @submit.prevent="addNewProduct()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" x-model="newProduct.name" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Barcode (Opsional)</label>
                        <input type="text" x-model="newProduct.barcode" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                        <input type="number" x-model.number="newProduct.price" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               min="0" step="100" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                        <input type="text" x-model="newProduct.unit" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="pcs, kg, liter, dll" value="pcs">
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="button" @click="showAddProductModal = false" 
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
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
    <div x-show="showEditModal" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop z-50 flex items-center justify-center p-4"
         @click.self="showEditModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full"
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Edit Item</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form @submit.prevent="saveEditedItem()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" x-model="editingItem.name" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               readonly>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Satuan</label>
                        <input type="number" x-model.number="editingItem.price" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               min="0" step="100">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <input type="number" x-model.number="editingItem.quantity" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               min="0.1" step="0.1">
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="button" @click="showEditModal = false" 
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
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

    <!-- Additional Cost Modal -->
    <div x-show="showAdditionalCostModal" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop z-50 flex items-center justify-center p-4"
         @click.self="showAdditionalCostModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full"
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Tambah Biaya Lain</h3>
                    <button @click="showAdditionalCostModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form @submit.prevent="addAdditionalCost()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <select x-model="newAdditionalCost.description" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                required>
                            <option value="">Pilih deskripsi...</option>
                            <option value="Hutang">Hutang</option>
                            <option value="Ongkir">Ongkos Kirim</option>
                            <option value="Biaya Admin">Biaya Admin</option>
                            <option value="Pajak Tambahan">Pajak Tambahan</option>
                            <option value="Diskon">Diskon</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div x-show="newAdditionalCost.description === 'Lainnya'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Custom</label>
                        <input type="text" x-model="newAdditionalCost.customDescription" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Masukkan deskripsi custom...">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                        <div class="relative">
                            <input type="number" x-model.number="newAdditionalCost.amount" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   min="0" step="100" required>
                            <div class="absolute right-3 top-2 text-sm text-gray-500">
                                <span x-show="newAdditionalCost.description === 'Diskon'">(-)</span>
                                <span x-show="newAdditionalCost.description !== 'Diskon'">(+)</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <span x-show="newAdditionalCost.description === 'Diskon'">Nilai diskon akan mengurangi total</span>
                            <span x-show="newAdditionalCost.description !== 'Diskon'">Biaya akan ditambahkan ke total</span>
                        </p>
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="button" @click="showAdditionalCostModal = false" 
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Price Edit Popup -->
    <div x-show="showPriceEditPopup" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95"
         class="price-edit-popup" 
         :style="`left: ${priceEditPosition.x}px; top: ${priceEditPosition.y}px;`"
         @click.away="closePriceEdit()">
        <div class="mb-3">
            <label class="block text-xs font-medium text-gray-700 mb-1">Edit Harga</label>
            <input type="number" x-model.number="priceEditValue" 
                   x-ref="priceEditInput"
                   class="w-full px-3 py-2 border border-blue-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                   min="0" step="100"
                   @keydown.enter="savePriceEdit()"
                   @keydown.escape="closePriceEdit()">
        </div>
        <div class="flex space-x-2">
            <button @click="savePriceEdit()" 
                    class="flex-1 px-3 py-1 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700 transition-colors">
                <i class="fas fa-check mr-1"></i>
                Simpan
            </button>
            <button @click="closePriceEdit()" 
                    class="flex-1 px-3 py-1 border border-gray-300 text-gray-700 text-xs rounded-md hover:bg-gray-50 transition-colors">
                Batal
            </button>
        </div>
    </div>
<!-- Tiered Pricing Info Modal -->
<div x-show="showTieredPricingModal" 
     x-transition:enter="transition ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop z-50 flex items-center justify-center p-4"
     @click.self="showTieredPricingModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto"
         x-transition:enter="transition ease-out duration-300 transform" 
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-layer-group text-orange-500 mr-2"></i>
                    Info Harga Bertingkat
                </h3>
                <button @click="showTieredPricingModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div x-show="selectedTieredItem">
                <h4 class="font-semibold text-lg mb-4" x-text="selectedTieredItem?.name"></h4>
                
                <div class="space-y-3">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex justify-between items-center">
                            <span class="text-blue-800 font-medium">Harga Dasar:</span>
                            <span class="text-blue-900 font-bold" x-text="formatCurrency(selectedTieredItem?.base_price)"></span>
                        </div>
                    </div>
                    
                    <div x-show="selectedTieredItem?.enable_tiered_pricing && selectedTieredItem?.tiered_prices?.length > 0">
                        <h5 class="font-medium text-gray-800 mb-2">Tingkat Harga:</h5>
                        <div class="space-y-2">
                            <template x-for="tier in selectedTieredItem?.tiered_prices" :key="tier.min_quantity">
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-orange-800 font-medium" x-text="'≥ ' + tier.min_quantity + ' unit'"></span>
                                        <span class="text-orange-900 font-bold" x-text="formatCurrency(tier.price)"></span>
                                    </div>
                                    <div x-show="tier.description" class="text-xs text-orange-700" x-text="tier.description"></div>
                                    <div class="text-xs text-green-600 font-medium mt-1">
                                        <span x-text="'Hemat: ' + formatCurrency((selectedTieredItem?.base_price || 0) - tier.price) + ' per unit'"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <h5 class="font-medium text-gray-800 mb-2">Batas Pembelian:</h5>
                        <div class="text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Minimum:</span>
                                <span x-text="(selectedTieredItem?.min_purchase || 1) + ' unit'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Maximum:</span>
                                <span x-text="selectedTieredItem?.max_purchase ? (selectedTieredItem.max_purchase + ' unit') : 'Unlimited'"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div x-show="selectedTieredItem?.applied_tier" class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <h5 class="font-medium text-green-800 mb-2">Tier Aktif:</h5>
                        <div class="text-sm text-green-700">
                            <div class="flex justify-between">
                                <span>Tier:</span>
                                <span x-text="'≥ ' + selectedTieredItem?.applied_tier?.min_quantity + ' unit'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Harga:</span>
                                <span x-text="formatCurrency(selectedTieredItem?.applied_tier?.price)"></span>
                            </div>
                            <div class="flex justify-between font-medium">
                                <span>Total Hemat:</span>
                                <span x-text="formatCurrency(selectedTieredItem?.discount_amount || 0)"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center pt-4">
                <button @click="showTieredPricingModal = false" 
                        class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<style>
    .tier-badge {
        background: linear-gradient(45deg, #f59e0b, #d97706);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .savings-badge {
        background: linear-gradient(45deg, #10b981, #059669);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        animation: pulse 2s infinite;
    }
    
    .tier-indicator {
        position: relative;
        overflow: hidden;
    }
    
    .tier-indicator::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    .purchase-limit-indicator {
        font-size: 10px;
        background: #f3f4f6;
        padding: 2px 4px;
        border-radius: 3px;
        color: #6b7280;
    }
    
    .tier-price-preview {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #f59e0b;
    }
    
    .cart-item-enhanced {
        border-left: 4px solid transparent;
        transition: all 0.3s ease;
    }
    
    .cart-item-enhanced.has-discount {
        border-left-color: #10b981;
        background: linear-gradient(90deg, #ecfdf5 0%, #ffffff 10%);
    }
    
    .quantity-stepper {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
    }
    
    .quantity-stepper.has-limits {
        border-color: #f59e0b;
    }
    
    .search-result-enhanced {
        transition: all 0.2s ease;
    }
    
    .search-result-enhanced:hover {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-left: 4px solid #0ea5e9;
    }
</style>
    <script>
function posSystem() {
    return {
        // Core data
        cart: [],
        searchQuery: '',
        searchResults: [],
        paidAmount: 0,
        additionalCosts: [],
        
        // State flags
        isProcessingTransaction: false,
        isSearching: false,
        
        // Modal states
        showAddProductModal: false,
        showEditModal: false,
        showAdditionalCostModal: false,
        showPriceEditPopup: false,
        showTieredPricingModal: false,
        selectedTieredItem: null,
        
        // Form states
        newAdditionalCost: {
            description: '',
            customDescription: '',
            amount: 0
        },
        
        // Constants
        quickAmounts: [10000, 20000, 50000, 100000],
        currentTime: '',
        currentDate: '',

        // ============= INITIALIZATION =============
        initializePOS() {
            this.loadCartFromStorage();
            this.loadAdditionalCostsFromStorage();
            this.updateDateTime();
            setInterval(() => this.updateDateTime(), 1000);
            
            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if (e.key === 'F1') {
                    e.preventDefault();
                    this.focusSearch();
                } else if (e.key === 'F2') {
                    e.preventDefault();
                    document.querySelector('input[x-model="paidAmount"]')?.focus();
                } else if (e.key === 'F3' && this.canProcessTransaction) {
                    e.preventDefault();
                    this.processTransaction();
                } else if (e.key === 'Escape') {
                    this.closeAllModals();
                }
            });
        },

        // ============= CART MANAGEMENT =============
        async addToCart(product, unitId, unitPrice, unitSymbol) {
            try {
                const productUnit = product.units.find(u => u.unit_id === unitId);
                if (!productUnit) {
                    this.showNotification('error', 'Informasi satuan tidak ditemukan');
                    return;
                }

                // Check if item already exists in cart
                const existingItemIndex = this.cart.findIndex(
                    (item) => item.product_id === product.id && item.unit_id === unitId
                );

                let quantity = 1;
                if (existingItemIndex >= 0) {
                    quantity = this.cart[existingItemIndex].quantity + 1;
                }

                // Validate and calculate tiered price
                const priceData = await this.calculateTieredPrice(product.id, unitId, quantity);
                
                if (!priceData.success) {
                    this.showNotification('warning', priceData.message);
                    return;
                }

                if (existingItemIndex >= 0) {
                    // Update existing item
                    this.cart[existingItemIndex].quantity = quantity;
                    this.cart[existingItemIndex].price = priceData.data.final_price;
                    this.cart[existingItemIndex].applied_tier = priceData.data.applied_tier;
                    this.cart[existingItemIndex].discount_amount = priceData.data.discount_amount;
                    this.cart[existingItemIndex].discount_percentage = priceData.data.discount_percentage;
                    
                    // Show tier change notification
                    if (priceData.data.applied_tier) {
                        this.showNotification('success', 
                            `Tier berubah! Diskon ${priceData.data.discount_percentage}% diterapkan`
                        );
                    }
                } else {
                    // Add new item
                    const newItem = {
                        id: Date.now() + Math.random(),
                        product_id: product.id,
                        unit_id: unitId,
                        name: product.name,
                        unit_symbol: unitSymbol,
                        base_price: priceData.data.base_price,
                        price: priceData.data.final_price,
                        quantity: quantity,
                        min_purchase: priceData.data.min_purchase || 1,
                        max_purchase: priceData.data.max_purchase || null,
                        enable_tiered_pricing: productUnit.enable_tiered_pricing || false,
                        tiered_prices: productUnit.tiered_prices || [],
                        applied_tier: priceData.data.applied_tier,
                        discount_amount: priceData.data.discount_amount || 0,
                        discount_percentage: priceData.data.discount_percentage || 0,
                    };
                    this.cart.push(newItem);
                }

                this.searchQuery = '';
                this.searchResults = [];
                this.saveCartToStorage();
                
                this.showNotification('success', `${product.name} ditambahkan ke keranjang`);
            } catch (error) {
                console.error('Error adding to cart:', error);
                this.showNotification('error', 'Terjadi kesalahan saat menambahkan produk');
            }
        },

        async updateQuantity(itemId, newQuantity) {
            if (newQuantity <= 0) {
                this.removeFromCart(itemId);
                return;
            }

            const itemIndex = this.cart.findIndex(item => item.id === itemId);
            if (itemIndex < 0) return;

            const item = this.cart[itemIndex];

            try {
                // Recalculate price with new quantity
                const priceData = await this.calculateTieredPrice(
                    item.product_id, 
                    item.unit_id, 
                    newQuantity
                );
                
                if (!priceData.success) {
                    this.showNotification('warning', priceData.message);
                    return;
                }

                const oldTier = item.applied_tier?.min_quantity;
                const newTier = priceData.data.applied_tier?.min_quantity;

                // Update item
                this.cart[itemIndex].quantity = newQuantity;
                this.cart[itemIndex].price = priceData.data.final_price;
                this.cart[itemIndex].applied_tier = priceData.data.applied_tier;
                this.cart[itemIndex].discount_amount = priceData.data.discount_amount;
                this.cart[itemIndex].discount_percentage = priceData.data.discount_percentage;

                this.saveCartToStorage();

                // Show notification if tier changed
                if (oldTier !== newTier) {
                    if (newTier) {
                        this.showNotification('success', 
                            `🎉 Tier berubah! Hemat ${this.formatCurrency(priceData.data.discount_amount)}`
                        );
                    } else if (oldTier && !newTier) {
                        this.showNotification('info', 'Tier dihapus - kuantitas di bawah minimum');
                    }
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
                this.showNotification('error', 'Terjadi kesalahan saat mengupdate quantity');
            }
        },

        removeFromCart(itemId) {
            const item = this.cart.find(i => i.id === itemId);
            this.cart = this.cart.filter(i => i.id !== itemId);
            this.saveCartToStorage();
            
            if (item) {
                this.showNotification('info', `${item.name} dihapus dari keranjang`);
            }
        },

        async clearCart() {
            const result = await Swal.fire({
                title: 'Kosongkan Keranjang?',
                text: "Semua item akan dihapus dari keranjang",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Kosongkan',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                this.cart = [];
                this.paidAmount = 0;
                this.clearStorage();
                this.showNotification('success', 'Keranjang dikosongkan');
            }
        },

        // ============= TIERED PRICING CALCULATION =============
        async calculateTieredPrice(productId, unitId, quantity) {
            try {
                const response = await fetch('/api/pos/calculate-tiered-price', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCSRFToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        unit_id: unitId,
                        quantity: quantity
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error calculating tiered price:', error);
                return {
                    success: false,
                    message: 'Terjadi kesalahan saat menghitung harga'
                };
            }
        },

        // ============= SEARCH FUNCTIONALITY =============
        async handleSearch() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                return;
            }

            this.isSearching = true;

            try {
                // Check if it's a barcode (8+ digits)
                if (/^\d{8,}$/.test(this.searchQuery)) {
                    await this.handleBarcodeSearch(this.searchQuery);
                } else {
                    await this.performProductSearch(this.searchQuery);
                }
            } catch (error) {
                console.error('Search error:', error);
                this.showNotification('error', 'Terjadi kesalahan saat mencari produk');
            } finally {
                this.isSearching = false;
            }
        },

        async handleBarcodeSearch(barcode) {
            try {
                const response = await fetch(`/api/pos/product-by-barcode?barcode=${encodeURIComponent(barcode)}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCSRFToken(),
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success && data.product) {
                    const product = data.product;
                    if (product.units && product.units.length > 0) {
                        const firstUnit = product.units[0];
                        await this.addToCart(product, firstUnit.unit_id, firstUnit.price, firstUnit.unit_symbol);
                    }
                } else {
                    await this.performProductSearch(barcode);
                }
            } catch (error) {
                await this.performProductSearch(barcode);
            }
        },

        async performProductSearch(query) {
            try {
                const response = await fetch(`/api/pos/search-product?query=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCSRFToken(),
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                this.searchResults = Array.isArray(data) ? data : [];
            } catch (error) {
                console.error('Search error:', error);
                this.searchResults = [];
            }
        },

        // ============= TRANSACTION PROCESSING =============
        async processTransaction() {
            if (!this.canProcessTransaction) return;

            const result = await Swal.fire({
                title: 'Konfirmasi Transaksi',
                html: this.generateTransactionSummaryHTML(),
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

            if (!result.isConfirmed) return;

            this.isProcessingTransaction = true;

            try {
                const response = await fetch("/api/pos/process", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": this.getCSRFToken(),
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        items: this.cart.map((item) => ({
                            product_id: item.product_id,
                            unit_id: item.unit_id,
                            quantity: item.quantity,
                            unit_price: item.price
                        })),
                        paid_amount: this.paidAmount,
                        additional_charges: this.additionalCosts.map(cost => ({
                            description: cost.description,
                            amount: cost.description === 'Diskon' ? -Math.abs(cost.amount) : cost.amount
                        }))
                    }),
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    await Swal.fire({
                        icon: "success",
                        title: "Transaksi Berhasil!",
                        html: this.generateSuccessTransactionHTML(data.transaction),
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-print mr-2"></i>Cetak Struk',
                        cancelButtonText: 'OK',
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(data.receipt_url, "_blank");
                        }
                    });

                    this.clearTransactionData();
                } else {
                    this.showNotification('error', data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error("Transaction error:", error);
                this.showNotification('error', "Terjadi kesalahan saat memproses transaksi");
            } finally {
                this.isProcessingTransaction = false;
            }
        },

        generateTransactionSummaryHTML() {
            let itemsHTML = '';
            let totalDiscount = 0;

            this.cart.forEach(item => {
                const itemDiscount = item.discount_amount || 0;
                totalDiscount += itemDiscount;
                
                itemsHTML += `
                    <div class="flex justify-between items-center py-1 text-sm">
                        <div class="flex-1">
                            <span class="font-medium">${item.name}</span>
                            <span class="text-gray-500">(${item.quantity} ${item.unit_symbol})</span>
                        </div>
                        <div class="text-right">
                            <div>${this.formatCurrency(item.price * item.quantity)}</div>
                            ${item.applied_tier ? 
                                `<div class="text-xs text-orange-600">Tier: ≥${item.applied_tier.min_quantity} unit</div>` 
                                : ''
                            }
                            ${itemDiscount > 0 ? 
                                `<div class="text-xs text-green-600">Hemat: ${this.formatCurrency(itemDiscount)}</div>` 
                                : ''
                            }
                        </div>
                    </div>
                `;
            });

            return `
                <div class="text-left bg-gray-50 p-4 rounded-lg">
                    <div class="space-y-2">
                        ${itemsHTML}
                        <hr class="my-2">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>${this.formatCurrency(this.subtotal)}</span>
                        </div>
                        ${totalDiscount > 0 ? `
                        <div class="flex justify-between text-green-600 font-medium">
                            <span>Total Hemat (Tier):</span>
                            <span>${this.formatCurrency(totalDiscount)}</span>
                        </div>
                        ` : ''}
                        ${this.totalAdditionalCosts !== 0 ? `
                        <div class="flex justify-between">
                            <span>Biaya Tambahan:</span>
                            <span>${this.formatCurrency(this.totalAdditionalCosts)}</span>
                        </div>
                        ` : ''}
                        <div class="flex justify-between border-t pt-2 font-bold">
                            <span>Total:</span>
                            <span class="text-blue-600">${this.formatCurrency(this.totalAmount)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Dibayar:</span>
                            <span>${this.formatCurrency(this.paidAmount)}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 font-bold">
                            <span>Kembalian:</span>
                            <span class="text-green-600">${this.formatCurrency(this.changeAmount)}</span>
                        </div>
                    </div>
                </div>
            `;
        },

        generateSuccessTransactionHTML(transaction) {
            return `
                <div class="text-left bg-green-50 p-4 rounded-lg">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">No. Transaksi:</span>
                            <span class="font-bold">${transaction.transaction_number}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total:</span>
                            <span>${this.formatCurrency(transaction.total_amount)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Dibayar:</span>
                            <span>${this.formatCurrency(transaction.paid_amount)}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span>Kembalian:</span>
                            <span class="font-bold text-green-600">${this.formatCurrency(transaction.change_amount)}</span>
                        </div>
                    </div>
                </div>
            `;
        },

        // ============= ADDITIONAL COSTS =============
        addAdditionalCost() {
            const description = this.newAdditionalCost.description === 'Lainnya' 
                ? this.newAdditionalCost.customDescription 
                : this.newAdditionalCost.description;

            if (!description || this.newAdditionalCost.amount <= 0) {
                this.showNotification('warning', 'Mohon isi deskripsi dan jumlah dengan benar');
                return;
            }

            const newCost = {
                id: Date.now() + Math.random(),
                description: description,
                amount: this.newAdditionalCost.amount
            };

            this.additionalCosts.push(newCost);
            this.saveAdditionalCostsToStorage();

            this.newAdditionalCost = {
                description: '',
                customDescription: '',
                amount: 0
            };

            this.showAdditionalCostModal = false;
            this.showNotification('success', `${description} berhasil ditambahkan`);
        },

        removeAdditionalCost(costId) {
            const cost = this.additionalCosts.find(c => c.id === costId);
            this.additionalCosts = this.additionalCosts.filter(c => c.id !== costId);
            this.saveAdditionalCostsToStorage();
            
            if (cost) {
                this.showNotification('info', `${cost.description} dihapus`);
            }
        },

        // ============= STORAGE MANAGEMENT =============
        saveCartToStorage() {
            localStorage.setItem('pos_cart', JSON.stringify(this.cart));
        },

        loadCartFromStorage() {
            const saved = localStorage.getItem('pos_cart');
            if (saved) {
                try {
                    this.cart = JSON.parse(saved);
                } catch (error) {
                    console.error('Error loading cart:', error);
                    this.cart = [];
                }
            }
        },

        saveAdditionalCostsToStorage() {
            localStorage.setItem('pos_additional_costs', JSON.stringify(this.additionalCosts));
        },

        loadAdditionalCostsFromStorage() {
            const saved = localStorage.getItem('pos_additional_costs');
            if (saved) {
                this.additionalCosts = JSON.parse(saved);
            }
        },

        clearStorage() {
            localStorage.removeItem('pos_cart');
            localStorage.removeItem('pos_additional_costs');
        },

        clearTransactionData() {
            this.cart = [];
            this.additionalCosts = [];
            this.paidAmount = 0;
            this.clearStorage();
        },

        // ============= PAYMENT METHODS =============
        setQuickAmount(amount) {
            this.paidAmount = amount;
        },

        setExactAmount() {
            if (this.totalAmount > 0) {
                this.paidAmount = this.totalAmount;
            }
        },

        // ============= COMPUTED PROPERTIES =============
        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.quantity * item.price), 0);
        },

        get totalSavings() {
            return this.cart.reduce((sum, item) => sum + (item.discount_amount || 0), 0);
        },

        get totalAdditionalCosts() {
            return this.additionalCosts.reduce((sum, cost) => {
                return cost.description === 'Diskon' ? sum - cost.amount : sum + cost.amount;
            }, 0);
        },

        get totalAmount() {
            return Math.max(0, this.subtotal + this.totalAdditionalCosts);
        },

        get changeAmount() {
            return Math.max(0, this.paidAmount - this.totalAmount);
        },

        get canProcessTransaction() {
            return this.cart.length > 0 && 
                   this.paidAmount >= this.totalAmount && 
                   !this.isProcessingTransaction;
        },

        // ============= UTILITY METHODS =============
        updateDateTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', {
                timeZone: 'Asia/Jakarta',
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            this.currentDate = now.toLocaleDateString('id-ID', {
                timeZone: 'Asia/Jakarta',
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
            }).format(amount);
        },

        getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        },

        focusSearch() {
            document.querySelector('input[x-model="searchQuery"]')?.focus();
        },

        closeAllModals() {
            this.showAddProductModal = false;
            this.showEditModal = false;
            this.showAdditionalCostModal = false;
            this.showPriceEditPopup = false;
            this.showTieredPricingModal = false;
        },

        showNotification(type, message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            Toast.fire({
                icon: type,
                title: message
            });
        }
    }
}
</script>
</body>
</html>