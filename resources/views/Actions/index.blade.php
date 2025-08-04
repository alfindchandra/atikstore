@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Sistem</h1>
            <p class="text-gray-600 mt-2">Kelola kategori produk dan satuan unit</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('categories.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Tambah Kategori
            </a>
            <a href="{{ route('units.create') }}" class="btn-success">
                <i class="fas fa-plus mr-2"></i>Tambah Unit
            </a>
        </div>
    </div>

    <!-- Management Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Category Management -->
        <div class="card overflow-hidden">
            <div class="card-header bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-tags text-2xl mr-3"></i>
                        <div>
                            <h3 class="text-xl font-semibold">Manajemen Kategori</h3>
                            <p class="text-blue-100 text-sm">Kelola kategori produk Anda</p>
                        </div>
                    </div>
                    <a href="{{ route('categories.index') }}" 
                       class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                        <i class="fas fa-external-link-alt mr-1"></i>Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="space-y-4">
                    <!-- Category Stats -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ \App\Models\Category::count() }}</div>
                            <div class="text-sm text-blue-600">Total Kategori</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ \App\Models\Category::has('products')->count() }}</div>
                            <div class="text-sm text-green-600">Kategori Aktif</div>
                        </div>
                    </div>

                    <!-- Recent Categories -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Kategori Terbaru</h4>
                        <div class="space-y-2">
                            @php
                                $recent_categories = \App\Models\Category::withCount('products')->latest()->take(5)->get();
                            @endphp
                            @forelse($recent_categories as $category)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-tag text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $category->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $category->products_count }} produk</div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('categories.edit', $category) }}" 
                                       class="text-blue-600 hover:text-blue-800 p-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-tags text-3xl mb-2"></i>
                                <p>Belum ada kategori</p>
                                <a href="{{ route('categories.create') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Tambah kategori pertama
                                </a>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="border-t pt-4">
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('categories.create') }}" 
                               class="flex items-center justify-center p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Tambah Kategori
                            </a>
                            <a href="{{ route('categories.index') }}" 
                               class="flex items-center justify-center p-3 border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                                <i class="fas fa-list mr-2"></i>Kelola Semua
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unit Management -->
        <div class="card overflow-hidden">
            <div class="card-header bg-gradient-to-r from-green-500 to-green-600 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-balance-scale text-2xl mr-3"></i>
                        <div>
                            <h3 class="text-xl font-semibold">Manajemen Unit</h3>
                            <p class="text-green-100 text-sm">Kelola satuan unit produk</p>
                        </div>
                    </div>
                    <a href="{{ route('units.index') }}" 
                       class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                        <i class="fas fa-external-link-alt mr-1"></i>Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="space-y-4">
                    <!-- Unit Stats -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ \App\Models\Unit::count() }}</div>
                            <div class="text-sm text-green-600">Total Unit</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ \App\Models\Unit::has('productUnits')->count() }}</div>
                            <div class="text-sm text-purple-600">Unit Digunakan</div>
                        </div>
                    </div>

                    <!-- Recent Units -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Unit Terbaru</h4>
                        <div class="space-y-2">
                            @php
                                $recent_units = \App\Models\Unit::withCount('productUnits')->latest()->take(5)->get();
                            @endphp
                            @forelse($recent_units as $unit)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="font-semibold text-green-600">{{ $unit->symbol }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $unit->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $unit->product_units_count }} produk</div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('units.edit', $unit) }}" 
                                       class="text-green-600 hover:text-green-800 p-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-balance-scale text-3xl mb-2"></i>
                                <p>Belum ada unit</p>
                                <a href="{{ route('units.create') }}" class="text-green-600 hover:text-green-800 text-sm">
                                    Tambah unit pertama
                                </a>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="border-t pt-4">
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('units.create') }}" 
                               class="flex items-center justify-center p-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Tambah Unit
                            </a>
                            <a href="{{ route('units.index') }}" 
                               class="flex items-center justify-center p-3 border-2 border-green-600 text-green-600 rounded-lg hover:bg-green-50 transition-colors">
                                <i class="fas fa-list mr-2"></i>Kelola Semua
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Overview -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Ringkasan Sistem</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-tags text-2xl text-blue-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ \App\Models\Category::count() }}</div>
                    <div class="text-sm text-gray-500">Total Kategori</div>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-balance-scale text-2xl text-green-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ \App\Models\Unit::count() }}</div>
                    <div class="text-sm text-gray-500">Total Unit</div>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-box text-2xl text-purple-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ \App\Models\Product::count() }}</div>
                    <div class="text-sm text-gray-500">Total Produk</div>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-warehouse text-2xl text-yellow-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ \App\Models\Product::where('is_active', true)->count() }}</div>
                    <div class="text-sm text-gray-500">Produk Aktif</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection