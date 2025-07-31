<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'POS Toko Kelontong' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <i class="fas fa-store text-2xl text-blue-600 mr-3"></i>
                    <h1 class="text-xl font-bold text-gray-800">POS Toko Kelontong</h1>
                </div>

                <!-- Navigation Menu -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('dashboard') }}" 
                           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('pos.index') }}" 
                           class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                            <i class="fas fa-cash-register mr-2"></i>Kasir
                        </a>
                        <a href="{{ route('products.index') }}" 
                           class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="fas fa-box mr-2"></i>Produk
                        </a>
                        <a href="{{ route('stock.adjustment') }}" 
                           class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                            <i class="fas fa-warehouse mr-2"></i>Stok
                        </a>
                        
                        <a href="{{ route('reports.index') }}" 
                           class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar mr-2"></i>Laporan
                        </a>
                        <a href="{{ route('actions.index') }}" 
                           class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-boxes-stacked mr-2"></i>Manajemen
                            
                        </a>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="mobile-menu-button">
                        <i class="fas fa-bars text-gray-500"></i>
                    </button>
                </div>

                <!-- User Info -->
                <div class="hidden md:block">
                    <div class="flex items-center">
                        <span class="text-gray-700 text-sm mr-4">
                            <i class="fas fa-calendar mr-1"></i>
                        {{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                        </span>
                        <div class="bg-blue-100 rounded-full p-2">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="mobile-menu hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-50">
                <a href="{{ route('dashboard') }}" 
                   class="mobile-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="{{ route('pos.index') }}" 
                   class="mobile-nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                    <i class="fas fa-cash-register mr-2"></i>Kasir
                </a>
                <a href="{{ route('products.index') }}" 
                   class="mobile-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-box mr-2"></i>Produk
                </a>
                <a href="{{ route('stock.index') }}" 
                   class="mobile-nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                    <i class="fas fa-warehouse mr-2"></i>Stok
                </a>
                <a href="{{ route('cashflow.index') }}" 
                   class="mobile-nav-link {{ request()->routeIs('cashflow.*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave mr-2"></i>Keuangan
                </a>
                <a href="{{ route('reports.index') }}" 
                   class="mobile-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar mr-2"></i>Manajemen
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-600 text-sm">
                © {{ date('Y') }} POS Toko Kelontong. All rights reserved.
            </div>
        </div>
    </footer>

    @livewireScripts

    <style>
        .nav-link {
            @apply text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200;
        }
        
        .nav-link.active {
            @apply text-blue-600 bg-blue-50;
        }
        
        .mobile-nav-link {
            @apply text-gray-600 hover:text-blue-600 block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200;
        }
        
        .mobile-nav-link.active {
            @apply text-blue-600 bg-blue-50;
        }
        
        .btn-primary {
            @apply bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
        }
        
        .btn-secondary {
            @apply bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
        }
        
        .btn-success {
            @apply bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
        }
        
        .btn-danger {
            @apply bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
        }
        
        .btn-warning {
            @apply bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
        }
        
        .card {
            @apply bg-white rounded-lg shadow-md border border-gray-200;
        }
        
        .card-header {
            @apply px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg;
        }
        
        .card-body {
            @apply px-6 py-4;
        }
        
        .form-input {
            @apply block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500;
        }
        
        .form-label {
            @apply text-sm font-medium text-gray-700 mb-1 block;
        }
        
        .table {
            @apply min-w-full divide-y divide-gray-200;
        }
        
        .table th {
            @apply px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
        }
        
        .table td {
            @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900;
        }
        
        .badge {
            @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
        }
        
        .badge-success {
            @apply bg-green-100 text-green-800;
        }
        
        .badge-warning {
            @apply bg-yellow-100 text-yellow-800;
        }
        
        .badge-danger {
            @apply bg-red-100 text-red-800;
        }
        
        .badge-info {
            @apply bg-blue-100 text-blue-800;
        }
    </style>

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });

        // Auto update time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            const timeElement = document.querySelector('.text-gray-700 span');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }
        
        // Update time every minute
        setInterval(updateTime, 60000);
        
        // Global SweetAlert configurations
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

        // Global functions
        window.showAlert = function(type, message) {
            Toast.fire({
                icon: type,
                title: message
            });
        };

        window.confirmDelete = function(callback) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        };

        // Format currency function
        window.formatCurrency = function(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        };

        // Format number function
        window.formatNumber = function(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        };
    </script>
</body>
</html>