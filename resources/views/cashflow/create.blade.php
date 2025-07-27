@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('cashflow.index') }}" class="text-gray-600 hover:text-gray-800 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Transaksi Keuangan</h1>
        </div>
        <p class="text-gray-600">Catat pemasukan atau pengeluaran toko</p>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Informasi Transaksi</h3>
        </div>
        
        <form action="{{ route('cashflow.store') }}" method="POST" id="cashflowForm">
            @csrf
            
            <div class="card-body space-y-6">
                <!-- Transaction Type -->
                <div>
                    <label class="form-label">
                        <i class="fas fa-exchange-alt text-gray-400 mr-2"></i>
                        Tipe Transaksi <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative">
                            <input type="radio" name="type" value="income" class="sr-only" required 
                                   {{ old('type') === 'income' ? 'checked' : '' }}
                                   onchange="updateTypeSelection()">
                            <div class="transaction-type-card border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-green-300 transition-colors">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Pemasukan</h4>
                                    <p class="text-sm text-gray-600">Uang masuk ke kas</p>
                                </div>
                            </div>
                        </label>
                        
                        <label class="relative">
                            <input type="radio" name="type" value="expense" class="sr-only" required 
                                   {{ old('type') === 'expense' ? 'checked' : '' }}
                                   onchange="updateTypeSelection()">
                            <div class="transaction-type-card border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-red-300 transition-colors">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Pengeluaran</h4>
                                    <p class="text-sm text-gray-600">Uang keluar dari kas</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="form-label">
                        <i class="fas fa-tags text-gray-400 mr-2"></i>
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <select id="category" name="category" class="form-input rounded-r-none @error('category') border-red-500 @enderror" required>
                            <option value="">Pilih Kategori</option>
                            <optgroup label="Pemasukan" id="incomeCategories" style="display: none;">
                                <option value="Penjualan" {{ old('category') === 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
                                <option value="Modal Tambahan" {{ old('category') === 'Modal Tambahan' ? 'selected' : '' }}>Modal Tambahan</option>
                                <option value="Lain-lain" {{ old('category') === 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                            </optgroup>
                            <optgroup label="Pengeluaran" id="expenseCategories" style="display: none;">
                                <option value="Pembelian Barang" {{ old('category') === 'Pembelian Barang' ? 'selected' : '' }}>Pembelian Barang</option>
                                <option value="Sewa" {{ old('category') === 'Sewa' ? 'selected' : '' }}>Sewa</option>
                                <option value="Listrik" {{ old('category') === 'Listrik' ? 'selected' : '' }}>Listrik</option>
                                <option value="Air" {{ old('category') === 'Air' ? 'selected' : '' }}>Air</option>
                                <option value="Gaji Karyawan" {{ old('category') === 'Gaji Karyawan' ? 'selected' : '' }}>Gaji Karyawan</option>
                                <option value="Transportasi" {{ old('category') === 'Transportasi' ? 'selected' : '' }}>Transportasi</option>
                                <option value="Perawatan" {{ old('category') === 'Perawatan' ? 'selected' : '' }}>Perawatan</option>
                                <option value="Lain-lain" {{ old('category') === 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                            </optgroup>
                        </select>
                        <input type="text" id="customCategory" placeholder="Kategori custom" 
                               class="form-input rounded-l-none border-l-0" style="display: none;">
                    </div>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="form-label">
                        <i class="fas fa-money-bill-wave text-gray-400 mr-2"></i>
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input 
                            type="number" 
                            id="amount" 
                            name="amount" 
                            value="{{ old('amount') }}"
                            class="form-input pl-10 @error('amount') border-red-500 @enderror" 
                            placeholder="0"
                            min="0.01"
                            step="0.01"
                            required
                            onkeyup="formatAmount(this)"
                        >
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div id="amountPreview" class="mt-1 text-sm text-gray-600"></div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="form-label">
                        <i class="fas fa-align-left text-gray-400 mr-2"></i>
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4"
                        class="form-input @error('description') border-red-500 @enderror" 
                        placeholder="Jelaskan detail transaksi..."
                        maxlength="500"
                        required
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-1 flex justify-between">
                        <p class="text-sm text-gray-500">Maksimal 500 karakter</p>
                        <p class="text-sm text-gray-400" id="charCount">0/500</p>
                    </div>
                </div>

                <!-- Transaction Date -->
                <div>
                    <label for="transaction_date" class="form-label">
                        <i class="fas fa-calendar text-gray-400 mr-2"></i>
                        Tanggal Transaksi <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        id="transaction_date" 
                        name="transaction_date" 
                        value="{{ old('transaction_date', now()->format('Y-m-d\TH:i')) }}"
                        class="form-input @error('transaction_date') border-red-500 @enderror" 
                        required
                    >
                    @error('transaction_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preview Card -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">
                        <i class="fas fa-eye mr-2"></i>
                        Preview Transaksi
                    </h4>
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <span id="previewTypeIcon" class="mr-2">
                                        <i class="fas fa-exchange-alt text-gray-400"></i>
                                    </span>
                                    <span id="previewType" class="font-medium text-gray-900">Tipe Transaksi</span>
                                </div>
                                <div class="text-sm text-gray-600 mb-1">
                                    <span id="previewCategory">Kategori</span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span id="previewDescription">Deskripsi transaksi</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold" id="previewAmount">Rp 0</div>
                                <div class="text-sm text-gray-500" id="previewDate">{{ now()->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="card-body border-t bg-gray-50">
                <div class="flex justify-between space-x-3">
                    <a href="{{ route('cashflow.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Transaksi
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Help Card -->
    <div class="card mt-6">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                Tips Pencatatan Keuangan
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-green-600 mb-2">
                        <i class="fas fa-arrow-up mr-1"></i>
                        Pemasukan
                    </h4>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li>• Hasil penjualan harian</li>
                        <li>• Modal tambahan dari pemilik</li>
                        <li>• Penjualan aset toko</li>
                        <li>• Pendapatan lain-lain</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-red-600 mb-2">
                        <i class="fas fa-arrow-down mr-1"></i>
                        Pengeluaran
                    </h4>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li>• Pembelian barang dagangan</li>
                        <li>• Biaya operasional (listrik, air)</li>
                        <li>• Gaji karyawan</li>
                        <li>• Biaya perawatan dan perbaikan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    updateTypeSelection();
    setupCharacterCounter();
    setupPreviewUpdates();
    
    // Focus on first input
    const firstRadio = document.querySelector('input[name="type"]');
    if (firstRadio) firstRadio.focus();
});

function updateTypeSelection() {
    const incomeRadio = document.querySelector('input[name="type"][value="income"]');
    const expenseRadio = document.querySelector('input[name="type"][value="expense"]');
    const incomeCard = incomeRadio.closest('label').querySelector('.transaction-type-card');
    const expenseCard = expenseRadio.closest('label').querySelector('.transaction-type-card');
    const incomeCategories = document.getElementById('incomeCategories');
    const expenseCategories = document.getElementById('expenseCategories');
    const categorySelect = document.getElementById('category');
    
    // Reset styles
    incomeCard.classList.remove('border-green-500', 'bg-green-50');
    expenseCard.classList.remove('border-red-500', 'bg-red-50');
    incomeCard.classList.add('border-gray-200');
    expenseCard.classList.add('border-gray-200');
    
    if (incomeRadio.checked) {
        incomeCard.classList.remove('border-gray-200');
        incomeCard.classList.add('border-green-500', 'bg-green-50');
        incomeCategories.style.display = 'block';
        expenseCategories.style.display = 'none';
    } else if (expenseRadio.checked) {
        expenseCard.classList.remove('border-gray-200');
        expenseCard.classList.add('border-red-500', 'bg-red-50');
        incomeCategories.style.display = 'none';
        expenseCategories.style.display = 'block';
    }
    
    // Reset category selection
    categorySelect.value = '';
    updatePreview();
}

function setupCharacterCounter() {
    const descriptionInput = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    
    descriptionInput.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = `${count}/500`;
        
        if (count > 450) {
            charCount.className = 'text-sm text-red-500';
        } else if (count > 400) {
            charCount.className = 'text-sm text-orange-500';
        } else {
            charCount.className = 'text-sm text-gray-400';
        }
    });
}

function formatAmount(input) {
    const value = parseFloat(input.value) || 0;
    const preview = document.getElementById('amountPreview');
    
    if (value > 0) {
        preview.textContent = `Format: ${new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value)}`;
    } else {
        preview.textContent = '';
    }
    
    updatePreview();
}

function setupPreviewUpdates() {
    // Listen to all form changes
    document.getElementById('cashflowForm').addEventListener('input', updatePreview);
    document.getElementById('cashflowForm').addEventListener('change', updatePreview);
}

function updatePreview() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const categorySelect = document.getElementById('category');
    const amountInput = document.getElementById('amount');
    const descriptionInput = document.getElementById('description');
    const dateInput = document.getElementById('transaction_date');
    
    // Update type
    const selectedType = document.querySelector('input[name="type"]:checked');
    const previewTypeIcon = document.getElementById('previewTypeIcon');
    const previewType = document.getElementById('previewType');
    const previewAmount = document.getElementById('previewAmount');
    
    if (selectedType) {
        if (selectedType.value === 'income') {
            previewTypeIcon.innerHTML = '<i class="fas fa-arrow-up text-green-600"></i>';
            previewType.textContent = 'Pemasukan';
            previewType.className = 'font-medium text-green-600';
        } else {
            previewTypeIcon.innerHTML = '<i class="fas fa-arrow-down text-red-600"></i>';
            previewType.textContent = 'Pengeluaran';
            previewType.className = 'font-medium text-red-600';
        }
    } else {
        previewTypeIcon.innerHTML = '<i class="fas fa-exchange-alt text-gray-400"></i>';
        previewType.textContent = 'Tipe Transaksi';
        previewType.className = 'font-medium text-gray-900';
    }
    
    // Update category
    const previewCategory = document.getElementById('previewCategory');
    previewCategory.textContent = categorySelect.value || 'Kategori';
    
    // Update amount
    const amount = parseFloat(amountInput.value) || 0;
    const formattedAmount = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
    
    previewAmount.textContent = formattedAmount;
    if (selectedType) {
        if (selectedType.value === 'income') {
            previewAmount.className = 'text-2xl font-bold text-green-600';
        } else {
            previewAmount.className = 'text-2xl font-bold text-red-600';
        }
    } else {
        previewAmount.className = 'text-2xl font-bold text-gray-900';
    }
    
    // Update description
    const previewDescription = document.getElementById('previewDescription');
    previewDescription.textContent = descriptionInput.value || 'Deskripsi transaksi';
    
    // Update date
    const previewDate = document.getElementById('previewDate');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        previewDate.textContent = date.toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

// Form validation
document.getElementById('cashflowForm').addEventListener('submit', function(e) {
    const type = document.querySelector('input[name="type"]:checked');
    const category = document.getElementById('category').value;
    const amount = document.getElementById('amount').value;
    const description = document.getElementById('description').value;
    
    if (!type) {
        e.preventDefault();
        showAlert('error', 'Pilih tipe transaksi');
        return false;
    }
    
    if (!category) {
        e.preventDefault();
        showAlert('error', 'Pilih kategori transaksi');
        return false;
    }
    
    if (!amount || parseFloat(amount) <= 0) {
        e.preventDefault();
        showAlert('error', 'Masukkan jumlah yang valid');
        return false;
    }
    
    if (!description.trim()) {
        e.preventDefault();
        showAlert('error', 'Deskripsi wajib diisi');
        return false;
    }
});
</script>
@endsection