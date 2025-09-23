<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'unit_id',
        'price',
        'conversion_rate',
        'is_base_unit',
        'min_purchase',
        'max_purchase',
        'enable_tiered_pricing'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'conversion_rate' => 'decimal:4',
        'is_base_unit' => 'boolean',
        'min_purchase' => 'integer',
        'max_purchase' => 'integer',
        'enable_tiered_pricing' => 'boolean'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tieredPrices(): HasMany
    {
        return $this->hasMany(TieredPrice::class);
    }

    // Helper method to get effective price for given quantity
    public function getEffectivePrice(int $quantity): float
    {
        if (!$this->enable_tiered_pricing || $this->tieredPrices->isEmpty()) {
            return $this->price;
        }

        $applicableTier = $this->tieredPrices()
            ->where('min_quantity', '<=', $quantity)
            ->orderBy('min_quantity', 'desc')
            ->first();

        return $applicableTier ? $applicableTier->price : $this->price;
    }

    // Helper method to check if quantity is within purchase limits
    public function isValidQuantity(int $quantity): bool
    {
        if ($quantity < $this->min_purchase) {
            return false;
        }

        if ($this->max_purchase && $quantity > $this->max_purchase) {
            return false;
        }

        return true;
    }
}