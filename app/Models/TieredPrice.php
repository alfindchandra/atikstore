<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TieredPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_unit_id',
        'min_quantity',
        'price',
        'description'
    ];

    protected $casts = [
        'min_quantity' => 'integer',
        'price' => 'decimal:2'
    ];

    public function productUnit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class);
    }

    // Scope for getting prices for specific quantity
    public function scopeForQuantity($query, int $quantity)
    {
        return $query->where('min_quantity', '<=', $quantity);
    }
}