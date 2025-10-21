<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.toko') }}</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">

<!-- Navbar -->
<nav class="bg-white shadow-md fixed w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600">
                    {{ config('app.toko') }}
                </a>
            </div>

            <!-- Menu Desktop -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                    <i class="fas fa-cash-register mr-2"></i>Kasir
                </a>
                
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" 
                            class="nav-link flex items-center justify-between w-full font-semibold focus:outline-none 
                                {{ request()->routeIs('debts.*', 'purchases.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                        <span>
                            <i class="fa-solid fa-file mr-2"></i>Data Center
                        </span>
                        <svg class="w-4 h-4 ml-2 transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20 origin-top-left">
                        <div class="py-1">
                            <a href="{{ route('purchases.index') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('purchases.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-200' }} ">
                                <i class="fa-solid fa-user mr-2 text-blue-500"></i>Sales
                            </a>
                            
                            <a href="{{ route('debts.index') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('debts.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-200' }}">
                                <i class="fa-solid fa-money-bill mr-2 text-green-500"></i>Hutang
                            </a>
                            
                        </div>
                    </div>
                </div>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" 
                            class="nav-link flex items-center justify-between w-full font-semibold focus:outline-none 
                                {{ request()->routeIs('products.*', 'stock.*', 'actions.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                        <span>
                            <i class="fas fa-warehouse mr-2"></i>Data Master
                        </span>
                        <svg class="w-4 h-4 ml-2 transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20 origin-top-left">
                        <div class="py-1">
                            <a href="{{ route('products.index') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('products.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-200' }} ">
                                <i class="fa-solid fa-box mr-2 text-blue-500"></i>Produk
                            </a>
                            
                            <a href="{{ route('stock.index') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('stock.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-200' }}">
                                <i class="fa-solid fa-truck mr-2 text-green-500"></i>Stok
                            </a>
                            <a href="{{ route('actions.index') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('actions.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-200' }}">
                                <i class="fas fa-chart-pie mr-2 text-purple-500"></i>Action
                            </a>
                        </div>
                    </div>
                </div>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" 
                            class="nav-link flex items-center justify-between w-full font-semibold focus:outline-none 
                                {{ request()->routeIs('reports.*', 'cashflow.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                        <span>
                            <i class="fas fa-chart-bar mr-2"></i>Laporan
                        </span>
                        <svg class="w-4 h-4 ml-2 transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20 origin-top-left">
                        <div class="py-1">
                            <a href="{{ route('reports.sales') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('reports.sales*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-200' }}">
                                <i class="fas fa-shopping-cart mr-2 text-blue-500"></i>Laporan Penjualan
                            </a>
                            
                            <a href="{{ route('cashflow.index') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('cashflow.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-200' }}">
                                <i class="fas fa-warehouse mr-2 text-green-500"></i>Laporan Keuangan
                            </a>
                            <a href="{{ route('reports.index') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('reports.index*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-200' }}">
                                <i class="fas fa-chart-pie mr-2 text-purple-500"></i>Laporan Analisis
                            </a>
                        </div>
                    </div>
                </div>

               
            </div>

            <!-- Info User -->
            <div class="hidden md:flex items-center space-x-4">
                <span class="text-gray-600 text-sm">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    <span id="current-time"></span>
                </span>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center focus:outline-none">
                        <div class="bg-blue-100 rounded-full p-2">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        
                        <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20 origin-top-right">
                        <div class="py-1">                          
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:text-blue-600 hover:bg-gray-200">
                                <i class="fas fa-sign-out-alt mr-2 text-red-500"></i>Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Hamburger Menu (Mobile) -->
            <div class="md:hidden flex items-center">
                <button id="menu-btn" class="text-gray-700 focus:outline-none text-2xl">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu Mobile -->
    <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg px-4 py-4 space-y-4">
    <a href="{{ route('dashboard') }}" class="block {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
    </a>
    <a href="{{ route('pos.index') }}" class="block {{ request()->routeIs('pos.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
        <i class="fas fa-cash-register mr-2"></i>Kasir
    </a>
    
    <!-- Data Center Mobile Menu -->
    <div class="border-t pt-2">
        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Data Center</p>
        <a href="{{ route('debts.index') }}" class="block pl-4 py-1 {{ request()->routeIs('debts.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <i class="fa-solid fa-money-bill mr-2 text-green-500"></i>Hutang
        </a>
    </div>
    
    <!-- Data Master Mobile Menu -->
    <div class="border-t pt-2">
        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Data Master</p>
        <a href="{{ route('products.index') }}" class="block pl-4 py-1 {{ request()->routeIs('products.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <i class="fas fa-box mr-2 text-blue-500"></i>Produk
        </a>
        <a href="{{ route('suppliers.index') }}" class="block pl-4 py-1 {{ request()->routeIs('suppliers.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <i class="fa-solid fa-truck mr-2 text-orange-500"></i>Supplier
        </a>
        <a href="{{ route('purchases.index') }}" class="block pl-4 py-1 {{ request()->routeIs('purchases.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <i class="fa-solid fa-shopping-cart mr-2 text-red-500"></i>Pembelian
        </a>
        <a href="{{ route('stock.index') }}" class="block pl-4 py-1 {{ request()->routeIs('stock.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <i class="fas fa-warehouse mr-2 text-green-500"></i>Stok
        </a>
        <a href="{{ route('actions.index') }}" class="block pl-4 py-1 {{ request()->routeIs('actions.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <i class="fas fa-chart-pie mr-2 text-purple-500"></i>Action
        </a>
    </div>
    
    <!-- Laporan Mobile Menu -->
    <div class="border-t pt-2">
        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Laporan</p>
        <a href="{{ route('reports.sales') }}" class="block pl-4 py-1 {{ request()->routeIs('reports.sales*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <i class="fas fa-shopping-cart mr-2 text-blue-500"></i>Laporan Penjualan
        </a>
        <a href="{{ route('cashflow.index') }}" class="block pl-4 py-1 {{ request()->routeIs('cashflow.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <i class="fas fa-warehouse mr-2 text-green-500"></i>Laporan Keuangan
        </a>
        <a href="{{ route('reports.index') }}" class="block pl-4 py-1 {{ request()->routeIs('reports.index*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <i class="fas fa-chart-pie mr-2 text-purple-500"></i>Laporan Analisis
        </a>
    </div>
    
    <div class="flex items-center justify-between border-t pt-4">
        <span class="text-gray-600 text-sm">
            <i class="fas fa-calendar-alt mr-1"></i>
            <span id="mobile-time"></span>
        </span>
        <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center focus:outline-none">
                        <div class="bg-blue-100 rounded-full p-2">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        
                        <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20 origin-top-right">
                        <div class="py-1">                          
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:text-blue-600 hover:bg-gray-200">
                                <i class="fas fa-sign-out-alt mr-2 text-red-500"></i>Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
        

            </div>
    </div>
</div>
</nav>

  <main class="flex-grow max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
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

        <div class="my-14">

            @yield('content')
        </div>
    </main>

<!-- Script untuk waktu real-time -->
<script>
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleString("id-ID", { dateStyle: "medium", timeStyle: "short" });
        document.getElementById("current-time").textContent = timeString;
        document.getElementById("mobile-time").textContent = timeString;
    }
    setInterval(updateTime, 1000);
    updateTime();

    // Toggle Mobile Menu
    document.getElementById("menu-btn").addEventListener("click", () => {
        document.getElementById("mobile-menu").classList.toggle("hidden");
    });
</script>
<script>
        // Mengambil elemen tombol menu dan dropdown
        const menuBtn = document.getElementById('menu-btn');
        const dropdownMenu = document.getElementById('dropdown-menu');

        // Menambahkan event listener saat tombol menu di-klik
        menuBtn.addEventListener('click', () => {
            // Toggle (munculkan/sembunyikan) class 'hidden' pada dropdown
            dropdownMenu.classList.toggle('hidden');
        });

        // Menutup dropdown jika pengguna mengklik di luar area menu
        window.addEventListener('click', (event) => {
            // Cek apakah yang diklik bukan tombol menu dan bukan bagian dari dropdown
            if (!menuBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                // Sembunyikan dropdown dengan menambahkan kembali class 'hidden'
                dropdownMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
