<div class="space-y-4">
    <!-- Transaction Header -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <h4 class="font-semibold text-gray-900">{{ $transaction->transaction_number }}</h4>
                <p class="text-sm text-gray-600">{{ $transaction->transaction_date->format('d/m/Y H:i:s') }}</p>
            </div>
            <div class="text-right">
                <span class="badge badge-success">{{ ucfirst($transaction->status) }}</span>
            </div>
        </div>
    </div>

    <!-- Transaction Items -->
    <div>
        <h5 class="font-semibold text-gray-900 mb-3">Item Pembelian</h5>
        <div class="space-y-2">
            @foreach($transaction->details as $detail)
            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                <div class="flex-1">
                    <p class="font-medium text-gray-900">{{ $detail->product->name }}</p>
                    <p class="text-sm text-gray-600">
                        {{ number_format($detail->quantity, 0, ',', '.') }} {{ $detail->unit->symbol }} 
                        × Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-gray-900">
                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Transaction Summary -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal:</span>
                <span class="font-medium">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($transaction->tax_amount > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Pajak:</span>
                <span class="font-medium">Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-lg font-semibold border-t pt-2">
                <span>Total:</span>
                <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Dibayar:</span>
                <span class="font-medium">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </div>
            @if($transaction->change_amount > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Kembalian:</span>
                <span class="font-medium">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex space-x-3 pt-4">
        <a href="{{ route('reports.transactions.edit', $transaction) }}" 
           class="btn-primary flex-1 text-center">
            <i class="fas fa-edit mr-2"></i>Edit Transaksi
        </a>
        <a href="{{ route('pos.receipt', $transaction) }}" 
           class="btn-secondary flex-1 text-center" target="_blank">
            <i class="fas fa-print mr-2"></i>Cetak Struk
        </a>
    </div>
</div>