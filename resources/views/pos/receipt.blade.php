<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $transaction->transaction_number }}</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        
        body {
            font-family: 'Courier New', monospace;
            max-width: 300px;
            margin: 20px auto;
            padding: 10px;
        }
        
        .receipt {
            border: 1px dashed #000;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .store-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .transaction-info {
            font-size: 11px;
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            font-size: 11px;
            margin-bottom: 10px;
        }
        
        .item-row td {
            padding: 3px 0;
        }
        
        .separator {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        .totals {
            font-size: 12px;
        }
        
        .totals td {
            padding: 2px 0;
        }
        
        .total-amount {
            font-size: 14px;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            font-size: 11px;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-print {
            background-color: #3B82F6;
            color: white;
        }
        
        .btn-close {
            background-color: #6B7280;
            color: white;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="store-name">{{ config('app.toko') }}</div>
            <div style="font-size: 10px;">Terima kasih atas kunjungan Anda</div>
        </div>

        <!-- Transaction Info -->
        <div class="transaction-info">
            <div>No: {{ $transaction->transaction_number }}</div>
            <div>Tanggal: {{ $transaction->transaction_date->format('d/m/Y H:i') }}</div>
            <div>Kasir: {{ auth()->user()->name ?? 'Admin' }}</div>
        </div>

        <div class="separator"></div>

        <!-- Items -->
        <table>
            @foreach($transaction->details as $detail)
            <tr class="item-row">
                <td colspan="3">{{ $detail->product->name }}</td>
            </tr>
            <tr class="item-row">
                <td style="padding-left: 10px;">
                    {{ number_format($detail->quantity, 0) }} {{ $detail->unit->symbol }} x {{ number_format($detail->unit_price, 0) }}
                </td>
                <td></td>
                <td style="text-align: right;">
                    {{ number_format($detail->subtotal, 0) }}
                </td>
            </tr>
            @endforeach
        </table>

        <div class="separator"></div>

        <!-- Totals -->
        <table class="totals">
            <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">Rp {{ number_format($transaction->subtotal, 0) }}</td>
            </tr>
            @if($transaction->tax_amount > 0)
            <tr>
                <td>Biaya Tambahan:</td>
                <td style="text-align: right;">Rp {{ number_format($transaction->tax_amount, 0) }}</td>
            </tr>
            @endif
            <tr class="total-amount">
                <td>TOTAL:</td>
                <td style="text-align: right;">Rp {{ number_format($transaction->total_amount, 0) }}</td>
            </tr>
            <tr>
                <td>Bayar:</td>
                <td style="text-align: right;">Rp {{ number_format($transaction->paid_amount, 0) }}</td>
            </tr>
            <tr>
                <td>Kembalian:</td>
                <td style="text-align: right;">Rp {{ number_format($transaction->change_amount, 0) }}</td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <div>*** TERIMA KASIH ***</div>
            <div style="margin-top: 5px;">Barang yang sudah dibeli</div>
            <div>tidak dapat ditukar/dikembalikan</div>
        </div>
    </div>

    <!-- Print Buttons -->
    <div class="button-container no-print">
        <button onclick="window.print()" class="btn btn-print">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" class="btn btn-close">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>