<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerDebt extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'phone',
        'address',
        'debt_amount',
        'paid_amount',
        'remaining_amount',
        'transaction_id',
        'notes',
        'items_purchased',
        'status',
        'debt_date',
        'due_date',
    ];

    protected $casts = [
        'debt_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'debt_date' => 'date',
        'due_date' => 'date',
        'items_purchased' => 'json',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    // Scope untuk hutang aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope untuk hutang yang sudah lunas
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Scope untuk hutang yang terlambat
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->format('Y-m-d'))
                    ->where('status', '!=', 'paid');
    }

    // Method untuk mengecek apakah hutang terlambat
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               $this->status !== 'paid';
    }

    // Method untuk menghitung persentase pembayaran
    public function getPaymentPercentage(): float
    {
        if ($this->debt_amount == 0) {
            return 0;
        }
        
        return ($this->paid_amount / $this->debt_amount) * 100;
    }

    // Method untuk update status berdasarkan pembayaran
    public function updateStatus(): void
    {
        if ($this->paid_amount >= $this->debt_amount) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partially_paid';
        } else {
            $this->status = 'active';
        }
        
        $this->save();
    }

    // Method untuk mendapatkan items yang dibeli dalam format yang mudah dibaca
    public function getFormattedItems(): string
    {
        if (!$this->items_purchased) {
            return 'Tidak ada detail barang';
        }

        if (is_string($this->items_purchased)) {
            return $this->items_purchased;
        }

        if (is_array($this->items_purchased)) {
            return collect($this->items_purchased)->map(function ($item) {
                return $item['name'] . ' (' . $item['quantity'] . ' ' . $item['unit'] . ')';
            })->implode(', ');
        }

        return 'Tidak ada detail barang';
    }
}