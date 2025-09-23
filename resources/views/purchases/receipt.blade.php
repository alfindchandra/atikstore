<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian - {{ $purchase->purchase_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { print-color-adjust: exact; }
        }
        
        .receipt-paper {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .receipt-content {
            background: white;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .receipt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .receipt-body {
            padding: 2rem;
        }
        
        .divider {
            border-bottom: 2px dashed #e5e7eb;
            margin: 1.5rem 0;
        }
        
        .receipt-table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .receipt-table th,
        .receipt-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .receipt-table th {
            background-color: #f9fafb;
            font-weight: 600;
        }
        
        .total-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body class="receipt-paper">
    <div class="max-w-4xl mx-auto">
        <!-- Print/Download Controls -->
        <div class="no-print mb-6 flex justify-between items-center">
            <a href="{{ route('purchases.show', $purchase) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition duration-200">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            
            <div class="flex space-x-3">
                <button onclick="window.print()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition duration-200">
                    <i class="fas fa-print"></i>
                    <span>Print</span>
                </button>
                <button onclick="downloadReceipt()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition duration-200">
                    <i class="fas fa-download"></i>
                    <span>Download</span>
                </button>
            </div>
        </div>

        <div class="receipt-content">
            <!-- Header -->
            <div class="receipt-header">
                <h1 class="text-3xl font-bold mb-2">STRUK PEMBELIAN</h1>
                <p class="text-xl opacity-90">{{ $purchase->purchase_number }}</p>
                <div class="mt-4 text-sm opacity-75">
                    <p>Tanggal: {{ $purchase->purchase_date->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="receipt-body">
                <!-- Store & Supplier Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-store mr-2 text-blue-600"></i>
                            Toko
                        </h3>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="font-semibold text-blue-900">POS Toko Kelontong</p>
                            <p class="text-blue-700 text-sm">Jl. Raya No. 123</p>
                            <p class="text-blue-700 text-sm">Telp: (021) 1234-5678</p>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-truck mr-2 text-orange-600"></i>
                            Supplier
                        </h3>
                        <div class="bg-orange-50 p-4 rounded-lg">
                            <p class="font-semibold text-orange-900">{{ $purchase->supplier->name }}</p>
                            @if($purchase->supplier->contact_person)
                            <p class="text-orange-700 text-sm">PIC: {{ $purchase->supplier->contact_person }}</p>
                            @endif
                            @if($purchase->supplier->phone)
                            <p class="text-orange-700 text-sm">Telp: {{ $purchase->supplier->phone }}</p>
                            @endif
                            @if($purchase->supplier->address)
                            <p class="text-orange-700 text-sm">{{ Str::limit($purchase->supplier->address, 50) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-box mr-2 text-green-600"></i>
                        Detail Pembelian ({{ $purchase->details->count() }} item)
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="receipt-table">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="text-left">No</th>
                                    <th class="text-left">Produk</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-right">Harga Satuan</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->details as $index => $detail)
                                <tr class="hover:bg-gray-50">
                                    <td class="font-medium text-gray-600">{{ $index + 1 }}</td>
                                    <td>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $detail->product->name }}</p>
                                            @if($detail->product->barcode)
                                            <p class="text-xs text-gray-500">{{ $detail->product->barcode }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                                            {{ $detail->unit->symbol }}
                                        </span>
                                    </td>
                                    <td class="text-center font-medium">
                                        {{ number_format($detail->quantity, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">
                                        Rp {{ number_format($detail->unit_cost, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right font-medium">
                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Summary -->
                <div class="total-section">
                    <h3 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-calculator mr-2"></i>
                        Ringkasan Pembayaran
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-lg">Subtotal:</span>
                            <span class="text-lg font-semibold">Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($purchase->tax_amount > 0)
                        <div class="flex justify-between items-center text-red-200">
                            <span>Pajak/Biaya Tambahan:</span>
                            <span class="font-semibold">+Rp {{ number_format($purchase->tax_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        @if($purchase->discount_amount > 0)
                        <div class="flex justify-between items-center text-green-200">
                            <span>Diskon:</span>
                            <span class="font-semibold">-Rp {{ number_format($purchase->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        <div class="border-t border-white border-opacity-30 pt-3 mt-4">
                            <div class="flex justify-between items-center text-xl">
                                <span class="font-bold">TOTAL PEMBAYARAN:</span>
                                <span class="font-bold text-2xl">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Receipt Image Section -->
                @if($purchase->receipt_image)
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-receipt mr-2 text-purple-600"></i>
                        Struk Asli
                    </h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-center">
                            <img src="{{ $purchase->receipt_image_url }}" 
                                 alt="Struk Pembelian" 
                                 class="max-w-full max-h-96 mx-auto rounded-lg shadow-md cursor-pointer hover:shadow-lg transition-shadow"
                                 onclick="openImageModal(this.src)">
                            <p class="text-sm text-gray-600 mt-2">Klik untuk memperbesar</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Notes Section -->
                @if($purchase->notes)
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>
                        Catatan
                    </h3>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <p class="text-yellow-800">{{ $purchase->notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Footer Info -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-600">
                        <div class="text-center">
                            <i class="fas fa-calendar-alt text-blue-500 mb-2"></i>
                            <p class="font-semibold">Tanggal Transaksi</p>
                            <p>{{ $purchase->purchase_date->format('d F Y') }}</p>
                            <p>{{ $purchase->purchase_date->format('H:i') }} WIB</p>
                        </div>
                        
                        <div class="text-center">
                            <i class="fas fa-hashtag text-green-500 mb-2"></i>
                            <p class="font-semibold">No. Pembelian</p>
                            <p class="font-mono">{{ $purchase->purchase_number }}</p>
                        </div>
                        
                        <div class="text-center">
                            <i class="fas fa-check-circle text-purple-500 mb-2"></i>
                            <p class="font-semibold">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $purchase->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Print Footer -->
                <div class="mt-8 text-center text-xs text-gray-400 border-t pt-4">
                    <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
                    <p>Sistem POS Toko Kelontong</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 no-print">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative max-w-5xl w-full">
                <button onclick="closeImageModal()" 
                        class="absolute -top-12 right-0 text-white hover:text-gray-300 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
                <img id="modalImage" src="" alt="Struk Pembelian" class="w-full h-auto rounded-lg shadow-2xl">
            </div>
        </div>
    </div>

    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        function downloadReceipt() {
            // Simple approach: trigger browser's print dialog with save as PDF option
            window.print();
        }

        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</body>
</html>