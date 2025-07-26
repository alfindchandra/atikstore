<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'barcode',
        'category_id',
        'description',
        'stock_alert_minimum',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productUnits(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function getBaseUnit()
    {
        return $this->productUnits()->where('is_base_unit', true)->first();
    }

    public function getTotalStockInBaseUnit()
    {
        $baseUnit = $this->getBaseUnit();
        if (!$baseUnit) return 0;

        return $this->stocks()->sum(function ($stock) use ($baseUnit) {
            $productUnit = $this->productUnits()
                ->where('unit_id', $stock->unit_id)
                ->first();
            
            return $stock->quantity * $productUnit->conversion_rate;
        });
    }

    public function isLowStock(): bool
    {
        return $this->getTotalStockInBaseUnit() <= $this->stock_alert_minimum;
    }
}