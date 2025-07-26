<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Struk - {{ $transaction->transaction_number }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 5mm;
            width: 70mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .store-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .store-info {
            font-size: 10px;
            margin-bottom: 2px;
        }
        
        .transaction-info {
            margin-bottom: 10px;
            font-size: 10px;
        }
        
        .items {
            margin-bottom: 10px;
        }
        
        .item {
            margin-bottom: 3px;
        }
        
        .item-name {
            font-weight: bold;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }
        
        .totals {
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-top: 10px;
        }
        
        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        .total-amount {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
            margin: 5px 0;
        }
        
        .payment {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 10px;
        }
        
        .thank-you {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .line {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="store-name">TOKO KELONTONG</div>
        <div class="store-info">Jl. Contoh No. 123</div>
        <div class="store-info">Telp: 021-12345678</div>
    </div>

    <div class="transaction-info">
        <div>No. Transaksi: {{ $transaction->transaction_number }}</div>
        <div>Tanggal: {{ $transaction->transaction_date->format('d/m/Y H:i:s') }}</div>
        <div>Kasir: Admin</div>
    </div>

    <div class="line"></div>

    <div class="items">
        @foreach($transaction->details as $detail)
        <div class="item">
            <div class="item-name">{{ $detail->product->name }}</div>
            <div class="item-details">
                <span>{{ number_format($detail->quantity, 0, ',', '.') }} {{ $detail->unit->symbol }} x {{ number_format($detail->unit_price, 0, ',', '.') }}</span>
                <span>{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="totals">
        <div class="total-line">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
        </div>
        
        @if($transaction->tax_amount > 0)
        <div class="total-line">
            <span>Pajak:</span>
            <span>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        
        <div class="total-line total-amount">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="payment">
        <div class="total-line">
            <span>Tunai:</span>
            <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
        </div>
        <div class="total-line">
            <span>Kembalian:</span>
            <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        <div class="thank-you">TERIMA KASIH</div>
        <div>Barang yang sudah dibeli</div>
        <div>tidak dapat ditukar/dikembalikan</div>
        <div style="margin-top: 10px;">{{ now()->format('d/m/Y H:i:s') }}</div>
    </div>
</body>
</html>

{{-- resources/views/pos/receipt.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <div class="card">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold">Struk Transaksi</h2>
                <div class="flex space-x-2">
                    <button onclick="window.print()" class="btn-primary text-sm">
                        <i class="fas fa-print mr-1"></i>
                        Print
                    </button>
                    <a href="{{ route('pos.print-receipt', $transaction->id) }}" target="_blank" class="btn-secondary text-sm">
                        <i class="fas fa-download mr-1"></i>
                        PDF
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="receipt" id="receipt">
                <!-- Store Header -->
                <div class="text-center mb-4 pb-3 border-b border-dashed border-gray-400">
                    <h3 class="text-lg font-bold">TOKO KELONTONG</h3>
                    <p class="text-sm text-gray-600">Jl. Contoh No. 123</p>
                    <p class="text-sm text-gray-600">Telp: 021-12345678</p>
                </div>

                <!-- Transaction Info -->
                <div class="mb-4 text-sm space-y-1">
                    <div class="flex justify-between">
                        <span>No. Transaksi:</span>
                        <span class="font-medium">{{ $transaction->transaction_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tanggal:</span>
                        <span>{{ $transaction->transaction_date->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kasir:</span>
                        <span>Admin</span>
                    </div>
                </div>

                <div class="border-b border-dashed border-gray-400 mb-3"></div>

                <!-- Items -->
                <div class="mb-4 space-y-2">
                    @foreach($transaction->details as $detail)
                    <div>
                        <div class="font-medium text-sm">{{ $detail->product->name }}</div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>{{ number_format($detail->quantity, 0, ',', '.') }} {{ $detail->unit->symbol }} x {{ number_format($detail->unit_price, 0, ',', '.') }}</span>
                            <span>{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="border-b border-dashed border-gray-400 mb-3"></div>

                <!-- Totals -->
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($transaction->tax_amount > 0)
                    <div class="flex justify-between">
                        <span>Pajak:</span>
                        <span>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between font-bold text-lg border-t border-b border-gray-800 py-2 my-2">
                        <span>TOTAL:</span>
                        <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Payment -->
                <div class="space-y-1 text-sm mb-4">
                    <div class="flex justify-between">
                        <span>Tunai:</span>
                        <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kembalian:</span>
                        <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="border-b border-dashed border-gray-400 mb-4"></div>

                <!-- Footer -->
                <div class="text-center text-sm space-y-1">
                    <p class="font-bold">TERIMA KASIH</p>
                    <p class="text-gray-600">Barang yang sudah dibeli</p>
                    <p class="text-gray-600">tidak dapat ditukar/dikembalikan</p>
                    <p class="text-xs text-gray-500 mt-3">{{ now()->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>
        
        <div class="card-body border-t bg-gray-50 no-print">
            <div class="flex justify-between">
                <a href="{{ route('pos.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Kembali ke Kasir
                </a>
                <button onclick="processNewTransaction()" class="btn-primary">
                    <i class="fas fa-plus mr-1"></i>
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    #receipt, #receipt * {
        visibility: visible;
    }
    
    #receipt {
        position: absolute;
        left: 0;
        top: 0;
        width: 80mm;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.2;
    }
    
    .no-print {
        display: none !important;
    }
}
</style>

<script>
function processNewTransaction() {
    if (confirm('Mulai transaksi baru?')) {
        window.location.href = '{{ route("pos.index") }}';
    }
}

// Auto print after 2 seconds if came from POS
if (document.referrer.includes('/pos') && !document.referrer.includes('/receipt')) {
    setTimeout(() => {
        if (confirm('Cetak struk sekarang?')) {
            window.print();
        }
    }, 1000);
}
</script>
@endsection