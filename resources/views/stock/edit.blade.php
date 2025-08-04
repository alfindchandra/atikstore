@extends('layouts.app')

@section('title', 'Edit Stok Produk')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Edit Stok Produk</h2>
                <p class="text-gray-600">{{ $product->name }}</p>
                @if($product->barcode)
                <p class="text-sm text-gray-500">Barcode: {{ $product->barcode }}</p>
                @endif
            </div>
            <a href="{{ route('stock.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>
    </div>

    <form action="{{ route('stock.update', $product) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')

        <!-- Product Info Summary -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-700">Kategori:</span>
                    <span class="text-gray-900">{{ $product->category->name }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Min Alert:</span>
                    <span class="text-gray-900">{{ rtrim(rtrim(number_format($product->stock_alert_minimum, 2), '0'), '.') }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Status:</span>
                    @if($product->isLowStock())
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            Stok Rendah
                        </span>
                    @else
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Normal
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stock Edit Form -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Stok per Satuan</h3>
            <div class="space-y-4">
                @foreach($product->stocks as $index => $stock)
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Satuan</label>
                            <div class="mt-1 text-sm text-gray-900">
                                <div class="font-medium">{{ $stock->unit->name }}</div>
                                <div class="text-gray-500">{{ $stock->unit->symbol }}</div>
                            </div>
                            <input type="hidden" name="stocks[{{ $index }}][unit_id]" value="{{ $stock->unit_id }}">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stok Saat Ini</label>
                            <div class="mt-1 text-sm text-gray-500">
                                {{ rtrim(rtrim(number_format($stock->quantity, 2), '0'), '.') }} {{ $stock->unit->symbol }}
                            </div>
                        </div>

                        <div>
                            <label for="stocks[{{ $index }}][quantity]" class="block text-sm font-medium text-gray-700">
                                Stok Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="stocks[{{ $index }}][quantity]"
                                   name="stocks[{{ $index }}][quantity]" 
                                   step="0.01" 
                                   min="0"
                                   value="{{ old('stocks.'.$index.'.quantity', rtrim(rtrim(number_format($stock->quantity, 2), '0'), '.')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                        </div>
                    </div>

                    @php
                        $productUnit = $product->productUnits->where('unit_id', $stock->unit_id)->first();
                    @endphp
                    @if($productUnit)
                    <div class="mt-3 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Harga satuan: Rp {{ number_format($productUnit->price, 0, ',', '.') }}</span>
                            @if($productUnit->is_base_unit)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                Satuan Dasar
                            </span>
                            @endif
                        </div>
                        <div class="mt-1">
                            Nilai stok saat ini: Rp {{ number_format($stock->quantity * $productUnit->price, 0, ',', '.') }}
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- Notes -->
        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan Perubahan</label>
            <textarea name="notes" id="notes" rows="3" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      placeholder="Alasan perubahan stok...">{{ old('notes') }}</textarea>
            <p class="mt-1 text-sm text-gray-500">Catatan akan disimpan dalam riwayat pergerakan stok</p>
        </div>

        <!-- Summary Info -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h4 class="font-medium text-blue-900 mb-2">Informasi:</h4>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• Perubahan stok akan tercatat dalam riwayat pergerakan</li>
                <li>• Masukkan 0 jika ingin mengosongkan stok untuk satuan tertentu</li>
                <li>• Pastikan jumlah stok sesuai dengan kondisi fisik barang</li>
            </ul>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('stock.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto calculate and display new stock value
    const quantityInputs = document.querySelectorAll('input[name*="[quantity]"]');
    
    quantityInputs.forEach(input => {
        input.addEventListener('input', function() {
            const container = this.closest('.border');
            const priceText = container.querySelector('.text-sm.text-gray-600 div:first-child');
            
            if (priceText) {
                const priceMatch = priceText.textContent.match(/Rp ([\d.,]+)/);
                if (priceMatch) {
                    const price = parseFloat(priceMatch[1].replace(/[.,]/g, ''));
                    const quantity = parseFloat(this.value) || 0;
                    const newValue = quantity * price;
                    
                    let valueDisplay = container.querySelector('.new-stock-value');
                    if (!valueDisplay) {
                        valueDisplay = document.createElement('div');
                        valueDisplay.className = 'new-stock-value mt-1 text-indigo-600 font-medium';
                        container.querySelector('.text-sm.text-gray-600').appendChild(valueDisplay);
                    }
                    
                    valueDisplay.textContent = `Nilai stok baru: Rp ${new Intl.NumberFormat('id-ID').format(newValue)}`;
                }
            }
        });
    });
});
</script>
@endsection