<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseTransaction extends Model
{
     use HasFactory;

    protected $fillable = [
        'purchase_number',
        'supplier_id',
        'purchase_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'receipt_image',
        'notes',
        'status'
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->purchase_number)) {
                $model->purchase_number = 'PUR-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseTransactionDetail::class);
    }

    public function getReceiptImageUrlAttribute()
    {
        return $this->receipt_image ? asset('storage/' . $this->receipt_image) : null;
    }
}
