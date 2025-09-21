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
                            <div class="p-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 text-lg" x-text="product.name"></h4>
                                      
                                        <p class="text-xs text-gray-500 mt-1 hidden md:block" x-show="product.barcode" x-text="'Barcode: ' + product.barcode"></p>
                                        <p class="text-xs text-gray-600 mt-1 ">
                                            <i class="fas fa-box mr-1"></i>
                                            <span x-text="product.stock_info?.length > 0 ? product.stock_info.map(s => s.quantity + ' ' + s.unit_symbol).join(', ') : 'Stok: N/A'"></span>
                                        </p>
                                        
                                    </div>
                                    <div class="ml-4">
                                        <div class="space-y-2">
                                            <template x-for="unit in product.units" :key="unit.unit_id">
                                                <button
                                                    @click="addToCart(product, unit.unit_id, unit.price, unit.unit_symbol)"
                                                    class="block w-full text-right bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg transition-all transform hover:scale-105"
                                                >
                                                    <div class="font-bold" x-text="formatCurrency(unit.price)+'/'+unit.unit_symbol"></div>
                                                </button>
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
                            class="text-red-600 hover:text-red-700 px-3 py-2 rounded-lg hover:bg-red-50 transition-colors"
                        >
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

                    <div class="space-y-4">
                       <template x-for="(item, index) in cart" :key="item.id">
                        <div class="cart-item flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-white rounded-xl shadow-sm border border-gray-200 mb-3 transition-transform transform hover:scale-[1.01] hover:shadow-lg">

                            <div class="flex-1 min-w-0 pr-4 mb-2 sm:mb-0">
                                <h4 class="font-semibold text-gray-900 text-base md:text-lg truncate" x-text="item.name "></h4>
                                <div class="flex items-center space-x-1 text-sm text-gray-500 mt-0.5">
                                    <span x-text="formatCurrency(item.price)"></span>
                                    <span class="text-xs text-gray-950" x-text="'/ ' + item.unit_symbol"></span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between w-full sm:w-auto space-x-4">
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="updateQuantity(item.id, item.quantity - 1)"
                                        class="w-7 h-7 bg-red-50 hover:bg-red-100 text-red-600 rounded-full flex items-center justify-center transition-colors">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <div class="text-sm font-semibold w-8 text-center" x-text="item.quantity"></div>
                                    <button
                                        @click="updateQuantity(item.id, item.quantity + 1)"
                                        class="w-7 h-7 bg-green-50 hover:bg-green-100 text-green-600 rounded-full flex items-center justify-center transition-colors">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                                
                                <div class="flex items-center space-x-2 ml-auto sm:ml-4">
                                    <div class="text-lg font-bold text-gray-900" 
                                        x-text="formatCurrency(item.quantity * item.price)"></div>
                                    <button
                                        @click="removeFromCart(item.id)"
                                        class="text-gray-400 hover:text-red-500 p-2 rounded-full transition-colors">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
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

    <script>
        function posSystem() {
            return {
                // State variables
                cart: [],
                searchQuery: '',
                searchResults: [],
                paidAmount: 0,
                additionalCosts: [],
                isProcessingTransaction: false,
                isSearching: false,
                currentTime: '',
                currentDate: '',
                
                // Modal states
                showAddProductModal: false,
                showEditModal: false,
                showAdditionalCostModal: false,
                showPriceEditPopup: false,
                
                // Edit states
                editingItem: {},
                editingItemId: null,
                priceEditItemId: null,
                priceEditValue: 0,
                priceEditPosition: { x: 0, y: 0 },
                
                // Form states
                newProduct: {
                    name: '',
                    barcode: '',
                    price: 0,
                    unit: 'pcs'
                },
                newAdditionalCost: {
                    description: '',
                    customDescription: '',
                    amount: 0
                },
                
                // Constants
                quickAmounts: [10000, 20000, 50000, 100000],

                // Initialize POS System
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
                            this.$refs.paidAmountInput?.focus();
                        } else if (e.key === 'F3' && this.canProcessTransaction) {
                            e.preventDefault();
                            this.processTransaction();
                        } else if (e.key === 'Escape') {
                            this.closeAllModals();
                        }
                    });
                },

                // Computed properties
                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.quantity * item.price), 0);
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

                // Utility methods
                updateDateTime() {
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
                    
                    this.currentTime = now.toLocaleTimeString('id-ID', timeOptions);
                    this.currentDate = now.toLocaleDateString('id-ID', dateOptions);
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

                // Storage methods
                saveCartToStorage() {
                    localStorage.setItem('pos_cart', JSON.stringify(this.cart));
                },

                loadCartFromStorage() {
                    const saved = localStorage.getItem('pos_cart');
                    if (saved) {
                        this.cart = JSON.parse(saved);
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

                // Search methods
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
                                this.addToCart(product, firstUnit.unit_id, firstUnit.price, firstUnit.unit_symbol);
                            }
                        } else {
                            await this.performProductSearch(barcode);
                        }
                    } catch (error) {
                        await this.performProductSearch(barcode);
                    }
                },

                async performProductSearch(query) {
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
                    
                    if (data.error) {
                        throw new Error(data.message || 'Terjadi kesalahan saat mencari produk');
                    }

                    this.searchResults = Array.isArray(data) ? data : [];
                },

                // Cart methods
                addToCart(product, unitId, unitPrice, unitSymbol) {
                    try {
                        const existingItemIndex = this.cart.findIndex(
                            (item) => item.product_id === product.id && item.unit_id === unitId
                        );

                        if (existingItemIndex >= 0) {
                            this.cart[existingItemIndex].quantity += 1;
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
                            this.cart.push(newItem);
                        }

                        this.searchQuery = '';
                        this.searchResults = [];
                        this.saveCartToStorage();
                        
                        this.showNotification('success', `${product.name} ditambahkan ke keranjang`);
                    } catch (error) {
                        console.error('Error adding to cart:', error);
                        this.showNotification('error', 'Terjadi kesalahan saat menambahkan produk ke keranjang');
                    }
                },

                updateQuantity(itemId, newQuantity) {
                    if (newQuantity <= 0) {
                        this.removeFromCart(itemId);
                        return;
                    }

                    const itemIndex = this.cart.findIndex(item => item.id === itemId);
                    if (itemIndex >= 0) {
                        this.cart[itemIndex].quantity = newQuantity;
                        this.saveCartToStorage();
                    }
                },

                removeFromCart(itemId) {
                    const item = this.cart.find(item => item.id === itemId);
                    this.cart = this.cart.filter(item => item.id !== itemId);
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
                        this.additionalCosts = [];
                        this.paidAmount = 0;
                        this.clearStorage();
                        this.showNotification('success', 'Keranjang dikosongkan');
                    }
                },

                // Price editing methods
                showPriceEdit(itemId, event) {
                    const item = this.cart.find(item => item.id === itemId);
                    if (!item) return;

                    const rect = event.target.getBoundingClientRect();
                    this.priceEditPosition = {
                        x: rect.left,
                        y: rect.bottom + 5
                    };

                    this.priceEditItemId = itemId;
                    this.priceEditValue = item.price;
                    this.showPriceEditPopup = true;

                    // Focus input after a short delay
                    this.$nextTick(() => {
                        this.$refs.priceEditInput?.focus();
                    });
                },

                savePriceEdit() {
                    if (this.priceEditItemId && this.priceEditValue > 0) {
                        const itemIndex = this.cart.findIndex(item => item.id === this.priceEditItemId);
                        if (itemIndex >= 0) {
                            this.cart[itemIndex].price = this.priceEditValue;
                            this.saveCartToStorage();
                            this.showNotification('success', 'Harga berhasil diubah');
                        }
                    }
                    this.closePriceEdit();
                },

                closePriceEdit() {
                    this.showPriceEditPopup = false;
                    this.priceEditItemId = null;
                    this.priceEditValue = 0;
                },

                // Edit item methods
                editCartItem(itemId) {
                    const item = this.cart.find(item => item.id === itemId);
                    if (!item) return;

                    this.editingItemId = itemId;
                    this.editingItem = { ...item };
                    this.showEditModal = true;
                },

                saveEditedItem() {
                    if (this.editingItemId && this.editingItem.price > 0 && this.editingItem.quantity > 0) {
                        const itemIndex = this.cart.findIndex(item => item.id === this.editingItemId);
                        if (itemIndex >= 0) {
                            this.cart[itemIndex] = { ...this.editingItem };
                            this.saveCartToStorage();
                            this.showNotification('success', 'Item berhasil diperbarui');
                        }
                    }
                    this.showEditModal = false;
                },

                // Additional cost methods
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

                    // Reset form
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

                // Product management
                addNewProduct() {
                    if (!this.newProduct.name || !this.newProduct.price || this.newProduct.price <= 0) {
                        this.showNotification('warning', 'Mohon isi nama produk dan harga dengan benar');
                        return;
                    }

                    // For demo purposes, add directly to cart as temporary product
                    const tempProduct = {
                        id: 'temp_' + Date.now(),
                        name: this.newProduct.name,
                        barcode: this.newProduct.barcode,
                        category: 'Produk Baru'
                    };

                    const newItem = {
                        id: Date.now() + Math.random(),
                        product_id: tempProduct.id,
                        unit_id: 'temp_unit',
                        name: tempProduct.name,
                        unit_symbol: this.newProduct.unit || 'pcs',
                        price: this.newProduct.price,
                        quantity: 1,
                    };

                    this.cart.push(newItem);
                    this.saveCartToStorage();
                    
                    // Reset form
                    this.newProduct = {
                        name: '',
                        barcode: '',
                        price: 0,
                        unit: 'pcs'
                    };

                    this.showAddProductModal = false;
                    this.showNotification('success', `${tempProduct.name} berhasil ditambahkan ke keranjang`);
                },

                // Payment methods
                setQuickAmount(amount) {
                    this.paidAmount = amount;
                },

                setExactAmount() {
                    if (this.totalAmount > 0) {
                        this.paidAmount = this.totalAmount;
                    }
                },

               async processTransaction() {
                    // ... (bagian ini tidak ada perubahan signifikan pada Alpine.js-nya, karena logic utamanya di backend)
                    if (!this.canProcessTransaction) return;

                    const result = await Swal.fire({
                        title: 'Konfirmasi Transaksi',
                        html: `
                            <div class="text-left bg-gray-50 p-4 rounded-lg">
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span>Subtotal:</span>
                                        <span>${this.formatCurrency(this.subtotal)}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Biaya Tambahan:</span>
                                        <span>${this.formatCurrency(this.totalAdditionalCosts)}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2">
                                        <span class="font-medium">Total:</span>
                                        <span class="font-bold text-blue-600">${this.formatCurrency(this.totalAmount)}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Dibayar:</span>
                                        <span>${this.formatCurrency(this.paidAmount)}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2">
                                        <span>Kembalian:</span>
                                        <span class="font-bold text-green-600">${this.formatCurrency(this.changeAmount)}</span>
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
                                    unit_price: item.price // Kirim harga per unit ke backend
                                })),
                                paid_amount: this.paidAmount,
                                tax_amount: this.totalAdditionalCosts, // Kirim biaya tambahan ke backend
                            }),
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            // Tampilkan dialog sukses dengan informasi transaksi
                            await Swal.fire({
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
                                                <span>${this.formatCurrency(data.transaction.total_amount)}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Dibayar:</span>
                                                <span>${this.formatCurrency(data.transaction.paid_amount)}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Biaya Tambahan:</span>
                                                <span>${this.formatCurrency(data.transaction.tax_amount)}</span>
                                            </div>
                                            <div class="flex justify-between border-t pt-2">
                                                <span>Kembalian:</span>
                                                <span class="font-bold text-green-600">${this.formatCurrency(data.transaction.change_amount)}</span>
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
                                    window.open(data.receipt_url, "_blank");
                                }
                            });

                            // Bersihkan keranjang setelah transaksi berhasil
                            this.clearTransactionData();
                        } else {
                            const errorMessage = data.message || 'Terjadi kesalahan tidak terduga.';
                            this.showNotification('error', errorMessage);
                        }
                    } catch (error) {
                        console.error("Transaction error:", error);
                        this.showNotification('error', "Terjadi kesalahan saat memproses transaksi: " + error.message);
                    } finally {
                        this.isProcessingTransaction = false;
                    }
                },
                clearTransactionData() {
                    this.cart = [];
                    this.additionalCosts = [];
                    this.paidAmount = 0;
                    this.clearStorage();
                },

                // UI Methods
                focusSearch() {
                    this.$refs.searchInput?.focus();
                },

                closeAllModals() {
                    this.showAddProductModal = false;
                    this.showEditModal = false;
                    this.showAdditionalCostModal = false;
                    this.showPriceEditPopup = false;
                },

                showNotification(type, message) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
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