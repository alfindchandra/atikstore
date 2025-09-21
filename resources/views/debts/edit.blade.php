@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-indigo-600">Edit Hutang Pelanggan</h1>
            <p class="text-gray-500 text-sm">
                {{ $debt->customer_name }}
            </p>
        </div>
        <a href="{{ route('debts.show', $debt) }}"
           class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-semibold text-indigo-600 flex items-center mb-4">
                    <i class="fas fa-edit mr-2"></i> Edit Informasi Hutang
                </h2>
                <form method="POST" action="{{ route('debts.update', $debt) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Pelanggan <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name"
                                   class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                   @error('customer_name') border-red-500 @enderror"
                                   value="{{ old('customer_name', $debt->customer_name) }}"
                                   placeholder="Masukkan nama pelanggan" required>
                            @error('customer_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                            <input type="text" name="phone"
                                   class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                   @error('phone') border-red-500 @enderror"
                                   value="{{ old('phone', $debt->phone) }}"
                                   placeholder="08xxxxxxxxxx">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="address" rows="2"
                                  class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                  @error('address') border-red-500 @enderror"
                                  placeholder="Alamat pelanggan (opsional)">{{ old('address', $debt->address) }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

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
                                           class="flex-1 p-2 rounded-r-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                           @error('debt_amount') border-red-500 @enderror"
                                           value="{{ number_format(old('debt_amount', $debt->debt_amount), 0, ',', '.') }}"
                                           min="1" step="1" required>
                                </div>
                                @error('debt_amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                @if($debt->paid_amount > 0)
                                    <p class="text-yellow-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Sudah ada pembayaran Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Hutang <span class="text-red-500">*</span></label>
                                <input type="date" name="debt_date"
                                       class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                       @error('debt_date') border-red-500 @enderror"
                                       value="{{ old('debt_date', $debt->debt_date->format('Y-m-d')) }}" required>
                                @error('debt_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jatuh Tempo</label>
                                <input type="date" name="due_date"
                                       class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                       @error('due_date') border-red-500 @enderror"
                                       value="{{ old('due_date', $debt->due_date ? $debt->due_date->format('Y-m-d') : '') }}">
                                @error('due_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Barang yang Dibeli</label>
                        <textarea name="items_purchased" rows="3"
                                  class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                  @error('items_purchased') border-red-500 @enderror"
                                  placeholder="Contoh: Beras 5kg, Minyak goreng 2L, Gula 1kg">{{ old('items_purchased', is_array($debt->items_purchased) ? $debt->getFormattedItems() : $debt->items_purchased) }}</textarea>
                        @error('items_purchased')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catatan</label>
                        <textarea name="notes" rows="2"
                                  class="mt-1 p-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                  @error('notes') border-red-500 @enderror"
                                  placeholder="Catatan tambahan (opsional)">{{ old('notes', $debt->notes) }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-between pt-4">
                        <button type="button" onclick="window.history.back()"
                                class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 text-sm">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-700 flex items-center mb-3">
                    <i class="fas fa-info-circle mr-2 text-indigo-500"></i> Informasi Status
                </h3>
                <div class="space-y-4 text-sm text-gray-600">
                    <div>
                        <p class="font-medium text-gray-500">Status Saat Ini</p>
                        <div>
                            @switch($debt->status)
                                @case('active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Aktif</span>
                                    @break
                                @case('partially_paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Sebagian Dibayar</span>
                                    @break
                                @case('paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Lunas</span>
                                    @break
                            @endswitch
                        </div>
                    </div>

                    <div>
                        <p class="font-medium text-gray-500">Jumlah Terbayar</p>
                        <div class="font-bold text-green-600">
                            Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}
                        </div>
                    </div>

                    <div>
                        <p class="font-medium text-gray-500">Sisa Hutang</p>
                        <div class="font-bold text-red-600">
                            Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
                        </div>
                    </div>

                    @if($debt->transaction)
                    <div>
                        <p class="font-medium text-gray-500">Transaksi Terkait</p>
                        <div class="text-sm">
                            <strong class="text-indigo-600">{{ $debt->transaction->transaction_number }}</strong>
                            <span class="text-gray-400">({{ $debt->transaction->transaction_date->format('d/m/Y H:i') }})</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($debt->paid_amount > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-4 rounded-lg shadow-md" role="alert">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-bold">Peringatan</p>
                        <p class="text-sm">Hutang ini sudah memiliki riwayat pembayaran. Mengubah jumlah hutang akan mempengaruhi sisa hutang yang harus dibayar.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection