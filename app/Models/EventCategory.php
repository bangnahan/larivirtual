<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'normal_price' => 'decimal:2',
        'early_bird_price' => 'decimal:2',
        'early_bird_end_date' => 'datetime',
        'reserved_bib_numbers' => 'array',
        'is_active' => 'boolean',
        'includes_jersey' => 'boolean',
        'includes_medal' => 'boolean',
    ];

    protected $attributes = [
        'includes_jersey' => true,
        'includes_medal' => true,
    ];

    protected static function booted(): void
    {
        static::creating(function (EventCategory $category) {
            if (empty($category->bib_start_number)) {
                $category->bib_start_number = 1001;
            }

            if ($category->quota === null) {
                $category->quota = 500;
            }

            if ($category->slots_taken === null) {
                $category->slots_taken = 0;
            }

            if ($category->is_active === null) {
                $category->is_active = true;
            }

            if ($category->includes_jersey === null) {
                $category->includes_jersey = true;
            }

            if ($category->includes_medal === null) {
                $category->includes_medal = true;
            }
        });
    }

    public function getPackageTypeLabelAttribute(): string
    {
        return match (true) {
            $this->includes_jersey && $this->includes_medal => 'Paket Lengkap (Medali + Jersey)',
            ! $this->includes_jersey && $this->includes_medal => 'Paket Medali Only',
            $this->includes_jersey && ! $this->includes_medal => 'Paket Jersey Only',
            default => 'Paket E-Pass (Digital Only)',
        };
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function customFields(): HasMany
    {
        return $this->hasMany(EventCustomField::class);
    }

    public function getCurrentPrice(): float
    {
        if ($this->early_bird_price && $this->early_bird_end_date && now()->lessThanOrEqualTo($this->early_bird_end_date)) {
            return (float) $this->early_bird_price;
        }

        return (float) $this->normal_price;
    }

    public function getAvailableSlotsAttribute(): int
    {
        return max(0, $this->quota - $this->slots_taken);
    }
}
