@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center">
            <a href="{{ route('purchases.index') }}" 
               class="mr-4 text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Pembelian</h1>
                <p class="text-gray-600">{{ $purchase->purchase_number }}</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('purchases.edit', $purchase) }}" 
               class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center space-x-2">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>
            @if($purchase->receipt_image)
            <button onclick="showReceiptImage('{{ $purchase->receipt_image_url }}')" 
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center space-x-2">
                <i class="fas fa-image"></i>
                <span>Lihat Struk</span>
            </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Purchase Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle mr-3 text-blue-600"></i>
                    Informasi Pembelian
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">No. Pembelian</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $purchase->purchase_number }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Pembelian</label>
                        <p class="text-lg text-gray-900">{{ $purchase->purchase_date->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Supplier</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $purchase->supplier->name }}</p>
                        @if($purchase->supplier->contact_person)
                        <p class="text-sm text-gray-600">PIC: {{ $purchase->supplier->contact_person }}</p>
                        @endif
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $purchase->status === 'completed' ? 'bg-green-100 text-green-800' : 
                               ($purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            <span class="w-2 h-2 mr-2 rounded-full 
                                {{ $purchase->status === 'completed' ? 'bg-green-400' : 
                                   ($purchase->status === 'pending' ? 'bg-yellow-400' : 'bg-red-400') }}"></span>
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </div>
                    
                    @if($purchase->notes)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Catatan</label>
                        <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $purchase->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Items Detail -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-box mr-3 text-green-600"></i>
                    Detail Item ({{ $purchase->details->count() }} item)
                </h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-medium text-gray-700">Produk</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-700">Satuan</th>
                                <th class="text-right py-3 px-4 font-medium text-gray-700">Jumlah</th>
                                <th class="text-right py-3 px-4 font-medium text-gray-700">Harga Satuan</th>
                                <th class="text-right py-3 px-4 font-medium text-gray-700">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->details as $detail)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-4 px-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $detail->product->name }}</p>
                                        @if($detail->product->barcode)
                                        <p class="text-sm text-gray-500">{{ $detail->product->barcode }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                                        {{ $detail->unit->symbol }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right font-medium">
                                    {{ number_format($detail->quantity, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-right">
                                    Rp {{ number_format($detail->unit_cost, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-right font-medium">
                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calculator mr-2 text-purple-600"></i>
                    Ringkasan
                </h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($purchase->tax_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pajak/Biaya Tambahan:</span>
                        <span class="font-medium text-red-600">Rp {{ number_format($purchase->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    @if($purchase->discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Diskon:</span>
                        <span class="font-medium text-green-600">-Rp {{ number_format($purchase->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold text-gray-900">Total:</span>
                            <span class="text-xl font-bold text-blue-600">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-truck mr-2 text-orange-600"></i>
                    Supplier
                </h3>
                
                <div class="space-y-3">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $purchase->supplier->name }}</h4>
                        @if($purchase->supplier->contact_person)
                        <p class="text-sm text-gray-600">PIC: {{ $purchase->supplier->contact_person }}</p>
                        @endif
                    </div>
                    
                    @if($purchase->supplier->phone)
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-phone mr-2"></i>
                        {{ $purchase->supplier->phone }}
                    </div>
                    @endif
                    
                    @if($purchase->supplier->email)
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-envelope mr-2"></i>
                        {{ $purchase->supplier->email }}
                    </div>
                    @endif
                    
                    @if($purchase->supplier->address)
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        {{ $purchase->supplier->address }}
                    </div>
                    @endif
                </div>
            </div>

            @if($purchase->receipt_image)
            <!-- Receipt Preview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-receipt mr-2 text-green-600"></i>
                    Struk Pembelian
                </h3>
                
                <div class="cursor-pointer" onclick="showReceiptImage('{{ $purchase->receipt_image_url }}')">
                    <img src="{{ $purchase->receipt_image_url }}" 
                         alt="Struk Pembelian" 
                         class="w-full rounded-lg shadow-sm hover:shadow-md transition-shadow">
                </div>
                
                <button onclick="showReceiptImage('{{ $purchase->receipt_image_url }}')" 
                        class="mt-3 w-full bg-green-100 hover:bg-green-200 text-green-700 font-medium py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-expand-alt mr-2"></i>
                    Lihat Ukuran Penuh
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal for Receipt Image -->
<div id="receiptModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Struk Pembelian - {{ $purchase->purchase_number }}</h3>
                    <button onclick="closeReceiptModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <img id="receiptImage" src="" alt="Struk Pembelian" class="w-full h-auto rounded-lg shadow-sm">
            </div>
        </div>
    </div>
</div>

<script>
function showReceiptImage(imageUrl) {
    document.getElementById('receiptImage').src = imageUrl;
    document.getElementById('receiptModal').classList.remove('hidden');
}

function closeReceiptModal() {
    document.getElementById('receiptModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('receiptModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReceiptModal();
    }
});
</script>
@endsection