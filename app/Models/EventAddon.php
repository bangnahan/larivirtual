<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventAddon extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_unlimited' => 'boolean',
        'is_active' => 'boolean',
        'weight_grams' => 'integer',
        'stock' => 'integer',
        'allocated_stock' => 'integer',
        'sort_order' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function orderAddons(): HasMany
    {
        return $this->hasMany(OrderAddon::class);
    }

    public function isUnlimited(): bool
    {
        return (bool) $this->is_unlimited;
    }

    public function getAvailableStockAttribute(): ?int
    {
        if ($this->isUnlimited()) {
            return null;
        }

        return max(0, ($this->stock ?? 0) - ($this->allocated_stock ?? 0));
    }

    public function isAvailable(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->isUnlimited()) {
            return true;
        }

        return ($this->available_stock ?? 0) > 0;
    }
}
