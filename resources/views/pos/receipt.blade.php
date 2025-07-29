@extends('layouts.app')

@section('title', 'Struk Transaksi')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-sm">
    <!-- Receipt Header -->
    <div class="p-6 text-center border-b border-gray-200">
        <h1 class="text-xl font-bold text-gray-900">TOKO SAYA</h1>
        <p class="text-sm text-gray-600">Jl. Contoh No. 123, Jakarta</p>
        <p class="text-sm text-gray-600">Telp: 021-12345678</p>
        <div class="mt-4 text-sm text-gray-500">
            <p>No. Transaksi: <span class="font-medium">{{ $transaction->transaction_number }}</span></p>
            <p>Tanggal: {{ $transaction->transaction_date->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <!-- Items -->
    <div class="p-6">
        <div class="space-y-3">
            @foreach($transaction->details as $detail)
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">{{ $detail->product->name }}</div>
                    <div class="text-xs text-gray-500">
                        {{ number_format($detail->quantity, 2) }} {{ $detail->unit->symbol }} × 
                        Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-900">
                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                </div>
            </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="mt-6 pt-4 border-t border-gray-200 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal:</span>
                <span class="text-gray-900">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </div>
            
            @if($transaction->tax_amount > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Pajak:</span>
                <span class="text-gray-900">Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            
            <div class="flex justify-between text-base font-semibold border-t pt-2">
                <span class="text-gray-900">Total:</span>
                <span class="text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Bayar:</span>
                <span class="text-gray-900">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </div>
            
            @if($transaction->change_amount > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Kembalian:</span>
                <span class="text-gray-900">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="mt-6 pt-4 border-t border-gray-200 text-center text-xs text-gray-500">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
            <p class="mt-2">{{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="p-6 border-t border-gray-200 flex space-x-3">
        <a href="{{ route('pos.print-receipt', $transaction) }}" target="_blank" 
           class="flex-1 px-4 py-2 bg-indigo-600 text-white text-center rounded-md hover:bg-indigo-700">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print
        </a>
        <button onclick="window.print()" 
                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-center rounded-md hover:bg-gray-50">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print Browser
        </button>
        <a href="{{ route('pos.index') }}" 
           class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-center rounded-md hover:bg-gray-50">
            Kembali ke POS
        </a>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .receipt-container, .receipt-container * {
        visibility: visible;
    }
    .receipt-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
}
</style>

<script>
// Auto focus for easy printing
document.addEventListener('DOMContentLoaded', function() {
    // Optional: Auto print when page loads (uncomment if needed)
    // window.print();
});
</script>
@endsection