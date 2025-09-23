@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Transaksi Pembelian</h1>
        <p class="text-gray-600">Kelola semua transaksi pembelian dari supplier</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('suppliers.index') }}" 
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center space-x-2">
            <i class="fas fa-truck"></i>
            <span>Kelola Supplier</span>
        </a>
        <a href="{{ route('purchases.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-200 flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Tambah Pembelian</span>
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                <p class="text-2xl font-bold text-gray-900">{{ $purchases->total() }}</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Bulan Ini</p>
                <p class="text-2xl font-bold text-green-600">{{ $purchases->where('purchase_date', '>=', now()->startOfMonth())->count() }}</p>
            </div>
            <div class="bg-green-100 p-3 rounded-lg">
                <i class="fas fa-calendar text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Pengeluaran</p>
                <p class="text-2xl font-bold text-red-600">Rp {{ number_format($purchases->sum('total_amount'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-red-100 p-3 rounded-lg">
                <i class="fas fa-money-bill-wave text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Selesai</p>
                <p class="text-2xl font-bold text-purple-600">{{ $purchases->where('status', 'completed')->count() }}</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-lg">
                <i class="fas fa-check-circle text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Purchases Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-xl font-semibold text-gray-900">Riwayat Pembelian</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        No. Pembelian
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Supplier
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Items
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($purchases as $purchase)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $purchase->purchase_number }}</div>
                        @if($purchase->receipt_image)
                        <div class="flex items-center mt-1">
                            <i class="fas fa-image text-green-500 mr-1"></i>
                            <span class="text-xs text-green-600">Ada struk</span>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $purchase->supplier->name }}</div>
                        @if($purchase->supplier->contact_person)
                        <div class="text-xs text-gray-500">{{ $purchase->supplier->contact_person }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $purchase->purchase_date->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $purchase->purchase_date->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</div>
                        @if($purchase->discount_amount > 0)
                        <div class="text-xs text-green-600">Diskon: Rp {{ number_format($purchase->discount_amount, 0, ',', '.') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                            {{ $purchase->details->count() }} item
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $purchase->status === 'completed' ? 'bg-green-100 text-green-800' : 
                               ($purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            <span class="w-2 h-2 mr-1 rounded-full 
                                {{ $purchase->status === 'completed' ? 'bg-green-400' : 
                                   ($purchase->status === 'pending' ? 'bg-yellow-400' : 'bg-red-400') }}"></span>
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('purchases.show', $purchase) }}" 
                               class="text-blue-600 hover:text-blue-900 transition-colors"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($purchase->receipt_image)
                            <button onclick="showReceiptImage('{{ $purchase->receipt_image_url }}')" 
                                    class="text-green-600 hover:text-green-900 transition-colors"
                                    title="Lihat Struk">
                                <i class="fas fa-image"></i>
                            </button>
                            @endif
                            <a href="{{ route('purchases.edit', $purchase) }}" 
                               class="text-yellow-600 hover:text-yellow-900 transition-colors"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deletePurchase({{ $purchase->id }})" 
                                    class="text-red-600 hover:text-red-900 transition-colors"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="text-gray-500">
                            <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                            <p class="text-lg font-medium">Belum ada transaksi pembelian</p>
                            <p class="text-sm">Silakan buat transaksi pembelian baru</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($purchases->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $purchases->links() }}
    </div>
    @endif
</div>

<!-- Modal for Receipt Image -->
<div id="receiptModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Struk Pembelian</h3>
                    <button onclick="closeReceiptModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
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

function deletePurchase(purchaseId) {
    Swal.fire({
        title: 'Hapus Transaksi Pembelian?',
        text: "Data transaksi akan dihapus permanen dan stok akan dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/purchases/${purchaseId}`;
            form.innerHTML = `
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection