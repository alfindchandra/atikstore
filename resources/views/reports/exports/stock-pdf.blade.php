<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok</title>
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
            font-size: 11px;
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
        
        .text-yellow {
            color: #f59e0b;
        }
        
        .text-red {
            color: #ef4444;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 3px;
            color: white;
        }
        
        .badge-success {
            background-color: #10b981;
        }
        
        .badge-warning {
            background-color: #f59e0b;
        }
        
        .badge-info {
            background-color: #3b82f6;
        }
        
        .low-stock-section {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        
        .low-stock-section h3 {
            color: #92400e;
            margin-top: 0;
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
        <h1>LAPORAN STOK BARANG</h1>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Summary -->
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">TOTAL PRODUK</div>
                <div class="value">{{ number_format($stockValue['total_products'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">NILAI INVENTORI</div>
                <div class="value text-green">Rp {{ number_format($stockValue['total_value'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">STOK MENIPIS</div>
                <div class="value text-yellow">{{ number_format($stockValue['low_stock_count'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Stock Table -->
    <h3>Detail Stok Produk</h3>
    <table>
        <thead>
            <tr>
                <th width="25%">Produk</th>
                <th width="15%">Kategori</th>
                <th width="20%">Stok per Satuan</th>
                <th width="15%">Stok Minimum</th>
                <th width="15%">Nilai Stok</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>
                    <div class="font-bold">{{ $product->name }}</div>
                    @if($product->barcode)
                    <div style="font-size: 10px; color: #666;">{{ $product->barcode }}</div>
                    @endif
                </td>
                <td>
                    <span class="badge badge-info">{{ $product->category->name ?? 'Tidak ada' }}</span>
                </td>
                <td>
                    @foreach($product->stocks as $stock)
                    <div style="font-size: 10px;">
                        {{ $stock->unit->name ?? 'Unknown' }}: {{ number_format($stock->quantity, 0, ',', '.') }}
                    </div>
                    @endforeach
                    @if($product->stocks->isEmpty())
                    <span style="color: #666; font-size: 10px;">Tidak ada stok</span>
                    @endif
                </td>
                <td class="text-center">
                    @php
                        $baseUnit = $product->getBaseUnit();
                    @endphp
                    {{ number_format($product->stock_alert_minimum, 0, ',', '.') }}
                    {{ $baseUnit ? $baseUnit->unit->symbol : 'unit' }}
                </td>
                <td class="text-right">
                    @php
                        $totalValue = 0;
                        foreach ($product->stocks as $stock) {
                            $productUnit = $product->productUnits->where('unit_id', $stock->unit_id)->first();
                            if ($productUnit) {
                                $totalValue += $stock->quantity * $productUnit->price;
                            }
                        }
                    @endphp
                    Rp {{ number_format($totalValue, 0, ',', '.') }}
                </td>
                <td class="text-center">
                    @if($product->isLowStock())
                        <span class="badge badge-warning">Menipis</span>
                    @else
                        <span class="badge badge-success">Normal</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Low Stock Alert Section -->
    @php
        $lowStockProducts = $products->filter(fn($p) => $p->isLowStock());
    @endphp
    
    @if($lowStockProducts->count() > 0)
    <div class="low-stock-section">
        <h3>⚠️ PERINGATAN STOK MENIPIS</h3>
        <p>Produk-produk berikut memerlukan perhatian segera karena stok sudah mencapai batas minimum:</p>
        
        <table style="background-color: white; margin-top: 10px;">
            <thead>
                <tr>
                    <th width="40%">Produk</th>
                    <th width="20%">Kategori</th>
                    <th width="20%">Stok Saat Ini</th>
                    <th width="20%">Stok Minimum</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockProducts as $product)
                <tr>
                    <td class="font-bold">{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? 'Tidak ada' }}</td>
                    <td class="text-center text-red">
                        @php
                            $baseUnit = $product->getBaseUnit();
                            $totalStock = $product->getTotalStockInBaseUnit();
                        @endphp
                        {{ number_format($totalStock, 0, ',', '.') }} {{ $baseUnit ? $baseUnit->unit->symbol : 'unit' }}
                    </td>
                    <td class="text-center">
                        {{ number_format($product->stock_alert_minimum, 0, ',', '.') }} {{ $baseUnit ? $baseUnit->unit->symbol : 'unit' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 15px; padding: 10px; background-color: white; border-radius: 3px;">
            <p style="margin: 0; font-size: 11px; color: #92400e;">
                <strong>Rekomendasi:</strong> Segera lakukan pengadaan ulang untuk produk-produk di atas untuk menghindari kehabisan stok.
            </p>
        </div>
    </div>
    @endif

    <!-- Summary by Category -->
    @php
        $categoryData = $products->groupBy(function($product) {
            return $product->category->name ?? 'Tidak ada kategori';
        });
    @endphp
    
    @if($categoryData->count() > 0)
    <div style="page-break-before: always;">
        <h3>Ringkasan per Kategori</h3>
        <table>
            <thead>
                <tr>
                    <th width="30%">Kategori</th>
                    <th width="20%">Jumlah Produk</th>
                    <th width="20%">Produk Stok Menipis</th>
                    <th width="30%">Total Nilai Stok</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categoryData as $categoryName => $categoryProducts)
                <tr>
                    <td class="font-bold">{{ $categoryName }}</td>
                    <td class="text-center">{{ $categoryProducts->count() }}</td>
                    <td class="text-center">
                        @php
                            $lowStockInCategory = $categoryProducts->filter(fn($p) => $p->isLowStock())->count();
                        @endphp
                        <span class="{{ $lowStockInCategory > 0 ? 'text-red' : '' }}">
                            {{ $lowStockInCategory }}
                        </span>
                    </td>
                    <td class="text-right">
                        @php
                            $categoryValue = 0;
                            foreach ($categoryProducts as $product) {
                                foreach ($product->stocks as $stock) {
                                    $productUnit = $product->productUnits->where('unit_id', $stock->unit_id)->first();
                                    if ($productUnit) {
                                        $categoryValue += $stock->quantity * $productUnit->price;
                                    }
                                }
                            }
                        @endphp
                        Rp {{ number_format($categoryValue, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
                <tr style="border-top: 2px solid #333; background-color: #f8f9fa;">
                    <td class="font-bold">TOTAL</td>
                    <td class="text-center font-bold">{{ $products->count() }}</td>
                    <td class="text-center font-bold text-red">{{ $stockValue['low_stock_count'] }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($stockValue['total_value'], 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>POS Toko Kelontong - Laporan Stok | Halaman <span class="pagenum"></span></p>
    </div>
</body>
</html>