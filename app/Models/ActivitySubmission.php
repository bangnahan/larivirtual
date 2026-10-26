<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivitySubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'activity_date',
        'activity_time',
        'distance_km',
        'duration_seconds',
        'calculated_pace',
        'proof_url',
        'proof_image_path',
        'notes',
        'validation_status',
        'reviewed_by_user_id',
        'admin_feedback',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'distance_km' => 'decimal:2',
        'duration_seconds' => 'integer',
        'calculated_pace' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saved(function (ActivitySubmission $submission) {
            $submission->participant?->recalculateProgress();
        });

        static::deleted(function (ActivitySubmission $submission) {
            $submission->participant?->recalculateProgress();
        });
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
