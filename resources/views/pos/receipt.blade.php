@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Struk Belanja</h1>
                <p class="text-gray-600">{{ config('app.name', 'Toko Kelontong') }}</p>
                <p class="text-sm text-gray-500">Jl. Contoh No. 123, Kota</p>
                <p class="text-sm text-gray-500">Telp: (021) 1234-5678</p>
            </div>

            <div class="border-t border-b border-gray-200 py-4 mb-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">No. Transaksi:</p>
                        <p class="font-semibold">{{ $transaction->transaction_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600">Tanggal:</p>
                        <p class="font-semibold">{{ $transaction->transaction_date->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 font-semibold">Item</th>
                            <th class="text-center py-2 font-semibold">Qty</th>
                            <th class="text-right py-2 font-semibold">Harga</th>
                            <th class="text-right py-2 font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->details as $detail)
                        <tr class="border-b border-gray-100">
                            <td class="py-2">
                                <div>
                                    <p class="font-medium">{{ $detail->product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $detail->unit->symbol ?? 'pcs' }}</p>
                                </div>
                            </td>
                            <td class="text-center py-2">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                            <td class="text-right py-2">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                            <td class="text-right py-2 font-medium">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-200 pt-4 space-y-2">
                <div class="flex justify-between">
                    <span class="font-medium">Subtotal:</span>
                    <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($transaction->tax_amount > 0)
                <div class="flex justify-between">
                    <span class="font-medium">Biaya tambahan:</span>
                    <span>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                
                @if($transaction->notes)
                <div class="text-sm text-gray-600 bg-gray-50 p-2 rounded">
                    {{ $transaction->notes }}
                </div>
                @endif

                
                <div class="border-t border-gray-300 pt-2">
                    <div class="flex justify-between text-lg font-bold">
                        <span>TOTAL:</span>
                        <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex justify-between">
                    <span>Bayar:</span>
                    <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between text-lg font-semibold text-green-600">
                    <span>Kembalian:</span>
                    <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="text-center mt-6 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">Terima kasih atas kunjungan Anda!</p>
                <p class="text-xs text-gray-500 mt-1">Barang yang sudah dibeli tidak dapat dikembalikan</p>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-center space-x-4">
        <a href="{{ route('pos.index') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Kasir
        </a>
        <button onclick="printReceipt()" 
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
            <i class="fas fa-print mr-2"></i>
            Print Struk
        </button>
    </div>
</div>

<script>
function printReceipt() {
    window.open('{{ route('pos.print-receipt', $transaction->id) }}', '_blank', 'width=400,height=600');
}
</script>
@endsection