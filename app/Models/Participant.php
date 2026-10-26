<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Participant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
        'custom_fields_data' => 'array',
        'waiver_accepted' => 'boolean',
        'waiver_accepted_at' => 'datetime',
        'total_distance_km' => 'decimal:2',
        'total_duration_seconds' => 'integer',
        'finished_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($participant) {
            if (empty($participant->qr_token)) {
                $participant->qr_token = Str::random(32);
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function jerseySize(): BelongsTo
    {
        return $this->belongsTo(JerseySize::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ActivitySubmission::class);
    }

    public function getEventAttribute(): ?Event
    {
        return $this->category?->event;
    }

    public function isFinisher(): bool
    {
        return $this->finisher_status === 'finisher';
    }

    public function targetDistance(): float
    {
        return (float) ($this->category?->distance_km ?? 5.0);
    }

    public function progressPercentage(): float
    {
        $target = $this->targetDistance();
        if ($target <= 0) {
            return 100.0;
        }

        $pct = ((float) $this->total_distance_km / $target) * 100;

        return min(100.0, round($pct, 1));
    }

    public function remainingDistance(): float
    {
        $target = $this->targetDistance();
        $remaining = $target - (float) $this->total_distance_km;

        return max(0.0, round($remaining, 2));
    }

    public function recalculateProgress(): void
    {
        $approvedSubmissions = $this->submissions()
            ->where('validation_status', 'approved')
            ->get();

        $totalDistance = $approvedSubmissions->sum('distance_km');
        $totalDuration = $approvedSubmissions->sum('duration_seconds');
        $target = $this->targetDistance();

        $isFinished = $totalDistance >= $target;

        $this->update([
            'total_distance_km' => $totalDistance,
            'total_duration_seconds' => $totalDuration,
            'finisher_status' => $isFinished ? 'finisher' : 'in_progress',
            'finished_at' => $isFinished ? ($this->finished_at ?? now()) : null,
        ]);
    }
}
