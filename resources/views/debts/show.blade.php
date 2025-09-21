@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Section - Mobile Optimized -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="text-2xl">👤</span>
                    Hutang {{ $debt->customer_name }}
                    </h1>
                    <p class="text-lg text-gray-600"></p>
                </div>
                
                <!-- Action Buttons - Mobile Stack -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('debts.index') }}" 
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Kembali
                    </a>
                    @if($debt->status !== 'paid')
                        <a href="{{ route('debts.edit', $debt) }}" 
                           class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 border border-transparent rounded-xl text-sm font-medium text-white hover:bg-blue-700 transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            
            <!-- Left Column - Payment Summary & Form -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Outstanding Balance Card -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                    <div class="text-center">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Sisa Hutang</h3>
                        <div class="text-3xl sm:text-4xl font-bold mb-4 {{ $debt->remaining_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                            Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
                        </div>
                        
                        <!-- Modern Progress Circle -->
                        <div class="relative mx-auto mb-6" style="width: 120px; height: 120px;">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="40" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                                <circle cx="50" cy="50" r="40" stroke="#10b981" stroke-width="8" fill="none"
                                        stroke-dasharray="251.2" 
                                        stroke-dashoffset="{{ 251.2 - (251.2 * $debt->getPaymentPercentage() / 100) }}"
                                        class="transition-all duration-1000 ease-out"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-xl font-bold text-gray-800">{{ number_format($debt->getPaymentPercentage(), 0) }}%</span>
                            </div>
                        </div>
                        
                        <!-- Amount Summary -->
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <p class="text-gray-500">Total Hutang</p>
                                <p class="font-semibold text-gray-900">Rp {{ number_format($debt->debt_amount, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-xl">
                                <p class="text-gray-500">Sudah Dibayar</p>
                                <p class="font-semibold text-green-600">Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                @if($debt->remaining_amount > 0)
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            Catat Pembayaran
                        </h3>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('debts.add-payment', $debt) }}" class="space-y-5">
                            @csrf
                            
                            <!-- Payment Amount -->
                            <div>
                                <label for="payment_amount" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Bayar</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                    <input type="number" name="payment_amount" id="payment_amount" 
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                           max="{{ $debt->remaining_amount }}" min="1" required
                                           placeholder="0">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Sisa: Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</p>
                               
                            </div>
                            
                            <!-- Payment Date -->
                            <div>
                                <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembayaran</label>
                                <input type="date" name="payment_date" id="payment_date" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                            
                            <!-- Notes -->
                            <div>
                                <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                <input type="text" name="notes" id="payment_notes" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                       placeholder="Contoh: Transfer Bank BNI">
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-green-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Right Column - Debt Details & Payment History -->
            <div class="lg:col-span-3 space-y-6">
                
                <!-- Debt Information -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Detail Hutang
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Debt Date -->
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-500">Tanggal Hutang</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $debt->debt_date->format('d F Y') }}</p>
                            </div>
                            
                            <!-- Due Date -->
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-500">Jatuh Tempo</p>
                                @if($debt->due_date)
                                    <p class="text-lg font-semibold {{ $debt->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $debt->due_date->format('d F Y') }}
                                    </p>
                                    @if($debt->isOverdue())
                                        <div class="flex items-center gap-1 text-red-600 text-sm font-medium mt-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                            </svg>
                                            Terlambat {{ $debt->due_date->diffInDays(now()) }} hari
                                        </div>
                                    @endif
                                @else
                                    <p class="text-lg text-gray-400">-</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Transaction Number -->
                        @if($debt->transaction)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-sm font-medium text-gray-500 mb-1">No. Transaksi</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $debt->transaction->transaction_number }}</p>
                        </div>
                        @endif
                        
                        <!-- Items -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-sm font-medium text-gray-500 mb-3">Barang yang Dibeli</p>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-gray-700 leading-relaxed">{{ $debt->getFormattedItems() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Riwayat Pembayaran
                            <span class="bg-white bg-opacity-20 px-2 py-1 rounded-full text-sm font-bold">{{ $debt->payments->count() }}</span>
                        </h3>
                    </div>
                    <div class="p-6">
                        @forelse($debt->payments as $payment)
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 rounded-xl bg-gray-50 mb-4 last:mb-0 border-l-4 border-green-400">
                                <div class="flex-grow">
                                    <div class="text-xl font-bold text-green-600 mb-1">
                                        + Rp {{ number_format($payment->payment_amount, 0, ',', '.') }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <p class="font-medium">{{ $payment->payment_date->format('d F Y') }}</p>
                                        @if($payment->notes)
                                            <p class="text-gray-500 mt-1">{{ $payment->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right mt-3 sm:mt-0 sm:ml-4">
                                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">
                                        {{ $payment->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-gray-500 text-lg">Belum ada pembayaran yang tercatat</p>
                                <p class="text-gray-400 text-sm mt-1">Pembayaran akan muncul di sini setelah dicatat</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setPaymentAmount(amount) {
        document.getElementById('payment_amount').value = Math.round(amount);
    }

    // Add smooth scroll behavior and animations
    document.addEventListener('DOMContentLoaded', function() {
        // Animate progress circle on load
        const progressCircle = document.querySelector('circle[stroke="#10b981"]');
        if (progressCircle) {
            progressCircle.style.strokeDashoffset = '251.2';
            setTimeout(() => {
                const percentage = {{ $debt->getPaymentPercentage() }};
                const offset = 251.2 - (251.2 * percentage / 100);
                progressCircle.style.strokeDashoffset = offset;
            }, 500);
        }

        // Add loading states to form submission
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                `;
            });
        }

        // Add focus effects to input fields
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-blue-500');
            });
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-blue-500');
            });
        });
    });
</script>
@endpush
@endsection