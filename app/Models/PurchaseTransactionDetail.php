<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseTransactionDetail extends Model
{
     use HasFactory;

    protected $fillable = [
        'purchase_transaction_id',
        'product_id',
        'unit_id',
        'quantity',
        'unit_cost',
        'subtotal'
    ];

    public function purchaseTransaction()
    {
        return $this->belongsTo(PurchaseTransaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
