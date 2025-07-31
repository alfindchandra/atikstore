<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-item .label {
            font-weight: bold;
            color: #666;
            font-size: 11px;
        }
        
        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .text-green {
            color: #10b981;
        }
        
        .text-blue {
            color: #3b82f6;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <p>Periode: {{ date('d/m/Y', strtotime($dateFrom)) }} - {{ date('d/m/Y', strtotime($dateTo)) }}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Summary -->
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">TOTAL PENDAPATAN</div>
                <div class="value text-green">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">TOTAL TRANSAKSI</div>
                <div class="value text-blue">{{ number_format($summary['total_transactions'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">ITEM TERJUAL</div>
                <div class="value">{{ number_format($summary['total_items_sold'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <h3>Detail Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th width="15%">No. Transaksi</th>
                <th width="15%">Tanggal</th>
                <th width="35%">Item</th>
                <th width="15%">Total</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td class="font-bold">{{ $transaction->transaction_number }}</td>
                <td>{{ $transaction->transaction_date->format('d/m/Y H:i') }}</td>
                <td>
                    @foreach($transaction->details as $index => $detail)
                        @if($index < 2)
                            {{ $detail->product->name }} 
                            ({{ number_format($detail->quantity, 0, ',', '.') }} {{ $detail->unit->symbol }})
                            @if(!$loop->last && $index < 1)<br>@endif
                        @endif
                    @endforeach
                    @if($transaction->details->count() > 2)
                        <br><small>+{{ $transaction->details->count() - 2 }} item lainnya</small>
                    @endif
                </td>
                <td class="text-right font-bold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                <td class="text-center">{{ ucfirst($transaction->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($transactions->count() > 15)
        <div class="page-break"></div>
        
        <!-- Transaction Details -->
        <h3>Detail Item Per Transaksi</h3>
        @foreach($transactions as $transaction)
            <div style="margin-bottom: 20px; border: 1px solid #ddd; padding: 10px;">
                <h4 style="margin: 0 0 10px 0;">{{ $transaction->transaction_number }} - {{ $transaction->transaction_date->format('d/m/Y H:i') }}</h4>
                <table style="margin-bottom: 10px;">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th width="15%">Jumlah</th>
                            <th width="20%">Harga Satuan</th>
                            <th width="20%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->details as $detail)
                        <tr>
                            <td>{{ $detail->product->name }}</td>
                            <td class="text-center">{{ number_format($detail->quantity, 0, ',', '.') }} {{ $detail->unit->symbol }}</td>
                            <td class="text-right">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #333;">
                            <td colspan="3" class="text-right font-bold">TOTAL:</td>
                            <td class="text-right font-bold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>POS Toko Kelontong - Laporan Penjualan | Halaman <span class="pagenum"></span></p>
    </div>
</body>
</html>