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
            <span>Biaya Tambahan</span>
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

