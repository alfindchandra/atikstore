@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-indigo-600">Tambah Hutang Pelanggan</h1>
            <p class="text-gray-500 text-sm">Catat piutang baru dari pelanggan dengan mudah</p>
        </div>
        <a href="{{ route('debts.index') }}"
           class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-semibold text-indigo-600 flex items-center mb-4">
                    <i class="fas fa-user-plus mr-2"></i> Informasi Hutang
                </h2>

                <form method="POST" action="{{ route('debts.store') }}" class="space-y-5">
                    @csrf

                    <!-- Data Pelanggan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Pelanggan <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name"
                                   class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                   value="{{ old('customer_name') }}" placeholder="Masukkan nama pelanggan" required>
                            @error('customer_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                            <input type="text" name="phone"
                                   class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                   value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="address" rows="2"
                                  class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                  placeholder="Alamat pelanggan (opsional)">{{ old('address') }}</textarea>
                    </div>

                    <!-- Sumber Transaksi -->
                    <div class="pt-4 border-t">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-receipt mr-2 text-indigo-500"></i> Sumber Transaksi
                        </h3>
                        <select name="transaction_id" id="transaction_id"
                                class="w-full p-2 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="">-- Pilih Transaksi (Opsional) --</option>
                            @foreach($todayTransactions as $transaction)
                                <option value="{{ $transaction->id }}" data-amount="{{ $transaction->total_amount }}">
                                    {{ $transaction->transaction_number }} - Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                    ({{ $transaction->transaction_date->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih transaksi jika hutang berasal dari transaksi hari ini</p>
                    </div>

                    <!-- Detail Hutang -->
                    <div class="pt-4 border-t">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-money-bill-wave mr-2 text-indigo-500"></i> Detail Hutang
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jumlah Hutang <span class="text-red-500">*</span></label>
                                <div class="flex rounded-lg shadow-sm mt-1">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
                                    <input type="number" name="debt_amount" id="debt_amount"
                                           class="flex-1 p-2 rounded-r-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                           value="{{ old('debt_amount') }}" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Hutang <span class="text-red-500">*</span></label>
                                <input type="date" name="debt_date"
                                       class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                       value="{{ old('debt_date', date('Y-m-d')) }}" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jatuh Tempo</label>
                                <input type="date" name="due_date"
                                       class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                       value="{{ old('due_date') }}">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Barang yang Dibeli</label>
                        <textarea name="items_purchased" id="items_purchased" rows="3"
                                  class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                  placeholder="Contoh: Beras 5kg, Minyak goreng 2L, Gula 1kg">{{ old('items_purchased') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Akan otomatis terisi jika memilih transaksi di atas</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catatan</label>
                        <textarea name="notes" rows="2"
                                  class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                  placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between pt-4">
                        <button type="button" onclick="window.history.back()"
                                class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 text-sm">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Hutang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info -->
        <div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-sm font-semibold text-gray-700 flex items-center mb-3">
                    <i class="fas fa-info-circle mr-2 text-indigo-500"></i> Panduan
                </h3>
                <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                    <li>Pilih transaksi hari ini untuk isi otomatis detail barang</li>
                    <li>Tetapkan jatuh tempo agar ada pengingat</li>
                    <li>Catat nomor telepon untuk follow up</li>
                    <li>Tambahkan catatan bila perlu</li>
                </ul>

                <!-- Preview -->
                <div id="transaction-preview" class="mt-4 hidden">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Preview Transaksi</h4>
                    <div id="transaction-details"
                         class="text-sm bg-gray-50 border rounded-lg p-3 text-gray-600"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const transactionSelect = document.getElementById("transaction_id");
    const debtAmount = document.getElementById("debt_amount");
    const itemsPurchased = document.getElementById("items_purchased");
    const preview = document.getElementById("transaction-preview");
    const details = document.getElementById("transaction-details");

    transactionSelect.addEventListener("change", () => {
        const option = transactionSelect.options[transactionSelect.selectedIndex];
        if (option.value) {
            debtAmount.value = option.dataset.amount;

            fetch(`{{ route('debts.transaction-items') }}?transaction_id=${option.value}`)
                .then(res => res.json())
                .then(data => {
                    itemsPurchased.value = data.items;
                    details.innerHTML = `
                        <p><strong>Total:</strong> Rp ${new Intl.NumberFormat('id-ID').format(data.total_amount)}</p>
                        <p><strong>Barang:</strong></p>
                        <p class="text-xs text-gray-500">${data.items}</p>
                    `;
                    preview.classList.remove("hidden");
                });
        } else {
            debtAmount.value = "";
            itemsPurchased.value = "";
            preview.classList.add("hidden");
        }
    });

    document.querySelector("input[name='debt_date']").addEventListener("change", (e) => {
        const date = new Date(e.target.value);
        date.setDate(date.getDate() + 7);
        document.querySelector("input[name='due_date']").value = date.toISOString().split("T")[0];
    });
});
</script>
@endpush
@endsection
