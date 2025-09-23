<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date|before_or_equal:now',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01|max:999999',
            'items.*.unit_cost' => 'required|numeric|min:0|max:999999999',
            'tax_amount' => 'nullable|numeric|min:0|max:999999999',
            'discount_amount' => 'nullable|numeric|min:0|max:999999999',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Supplier wajib dipilih',
            'supplier_id.exists' => 'Supplier tidak valid',
            'purchase_date.required' => 'Tanggal pembelian wajib diisi',
            'purchase_date.date' => 'Format tanggal tidak valid',
            'purchase_date.before_or_equal' => 'Tanggal pembelian tidak boleh lebih dari hari ini',
            'items.required' => 'Minimal harus ada 1 item pembelian',
            'items.array' => 'Format item tidak valid',
            'items.min' => 'Minimal harus ada 1 item pembelian',
            'items.*.product_id.required' => 'Produk wajib dipilih',
            'items.*.product_id.exists' => 'Produk tidak valid',
            'items.*.unit_id.required' => 'Satuan wajib dipilih',
            'items.*.unit_id.exists' => 'Satuan tidak valid',
            'items.*.quantity.required' => 'Jumlah wajib diisi',
            'items.*.quantity.numeric' => 'Jumlah harus berupa angka',
            'items.*.quantity.min' => 'Jumlah minimal 0.01',
            'items.*.quantity.max' => 'Jumlah terlalu besar',
            'items.*.unit_cost.required' => 'Harga satuan wajib diisi',
            'items.*.unit_cost.numeric' => 'Harga satuan harus berupa angka',
            'items.*.unit_cost.min' => 'Harga satuan tidak boleh negatif',
            'items.*.unit_cost.max' => 'Harga satuan terlalu besar',
            'tax_amount.numeric' => 'Pajak harus berupa angka',
            'tax_amount.min' => 'Pajak tidak boleh negatif',
            'tax_amount.max' => 'Pajak terlalu besar',
            'discount_amount.numeric' => 'Diskon harus berupa angka',
            'discount_amount.min' => 'Diskon tidak boleh negatif',
            'discount_amount.max' => 'Diskon terlalu besar',
            'receipt_image.image' => 'File harus berupa gambar',
            'receipt_image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'receipt_image.max' => 'Ukuran gambar maksimal 5MB',
            'notes.max' => 'Catatan maksimal 1000 karakter',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate that each item has unique product-unit combination
            $items = $this->input('items', []);
            $combinations = [];
            
            foreach ($items as $index => $item) {
                if (isset($item['product_id']) && isset($item['unit_id'])) {
                    $combination = $item['product_id'] . '-' . $item['unit_id'];
                    
                    if (in_array($combination, $combinations)) {
                        $validator->errors()->add("items.{$index}", 'Kombinasi produk dan satuan sudah ada di item lain');
                    }
                    
                    $combinations[] = $combination;
                }
            }

            // Validate total amount
            $subtotal = 0;
            foreach ($items as $item) {
                if (isset($item['quantity']) && isset($item['unit_cost'])) {
                    $subtotal += $item['quantity'] * $item['unit_cost'];
                }
            }

            $taxAmount = $this->input('tax_amount', 0);
            $discountAmount = $this->input('discount_amount', 0);
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            if ($totalAmount <= 0) {
                $validator->errors()->add('total_amount', 'Total pembayaran harus lebih dari 0');
            }

            if ($discountAmount > $subtotal) {
                $validator->errors()->add('discount_amount', 'Diskon tidak boleh lebih besar dari subtotal');
            }
        });
    }
}

class UpdatePurchaseRequest extends StorePurchaseRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        
        // Make receipt_image optional for updates
        $rules['receipt_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120';
        
        return $rules;
    }
}