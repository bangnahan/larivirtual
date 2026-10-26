<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'event_start_date' => 'datetime',
        'event_end_date' => 'datetime',
        'registration_open_date' => 'datetime',
        'registration_close_date' => 'datetime',
        'race_start_date' => 'datetime',
        'race_end_date' => 'datetime',
        'racepack_delivery_start_date' => 'datetime',
        'racepack_delivery_end_date' => 'datetime',
        'rpc_start_date' => 'datetime',
        'rpc_end_date' => 'datetime',
        'auto_assign_bib' => 'boolean',
        'is_meta_capi_enabled' => 'boolean',
    ];

    /**
     * Cek apakah event ini mengaktifkan background e-BIB kustom
     */
    public function hasEbibBackground(): bool
    {
        return ! empty($this->ebib_background_path);
    }

    /**
     * Cek apakah event ini mengaktifkan background e-sertifikat kustom
     */
    public function hasCertificateBackground(): bool
    {
        return ! empty($this->certificate_background_path);
    }

    /**
     * Dapatkan base64 Data URI untuk gambar background e-BIB (aman untuk DomPDF)
     */
    public function getEbibBackgroundBase64(): ?string
    {
        if (! empty($this->ebib_background_path) && Storage::disk('public')->exists($this->ebib_background_path)) {
            $content = Storage::disk('public')->get($this->ebib_background_path);
            $mime = Storage::disk('public')->mimeType($this->ebib_background_path) ?: 'image/jpeg';

            return 'data:'.$mime.';base64,'.base64_encode($content);
        }

        return null;
    }

    /**
     * Dapatkan base64 Data URI untuk gambar background e-sertifikat (aman untuk DomPDF)
     */
    public function getCertificateBackgroundBase64(): ?string
    {
        if (! empty($this->certificate_background_path) && Storage::disk('public')->exists($this->certificate_background_path)) {
            $content = Storage::disk('public')->get($this->certificate_background_path);
            $mime = Storage::disk('public')->mimeType($this->certificate_background_path) ?: 'image/jpeg';

            return 'data:'.$mime.';base64,'.base64_encode($content);
        }

        return null;
    }

    /**
     * Cek apakah event ini mengaktifkan dan mengonfigurasi Meta CAPI
     */
    public function hasMetaCapi(): bool
    {
        return (bool) $this->is_meta_capi_enabled
            && ! empty($this->meta_pixel_id)
            && ! empty($this->meta_capi_access_token);
    }

    /**
     * Dapatkan jadwal pengiriman race pack yang diformat rapi
     */
    public function getRacepackDeliveryScheduleFormatted(): string
    {
        if (! empty($this->racepack_delivery_schedule)) {
            return $this->racepack_delivery_schedule;
        }

        if ($this->racepack_delivery_start_date) {
            $start = $this->racepack_delivery_start_date->format('d M Y');
            if ($this->racepack_delivery_end_date) {
                return $start.' - '.$this->racepack_delivery_end_date->format('d M Y');
            }

            return $start;
        }

        if ($this->rpc_start_date) {
            $start = $this->rpc_start_date->format('d M Y');
            if ($this->rpc_end_date) {
                return $start.' - '.$this->rpc_end_date->format('d M Y');
            }

            return $start;
        }

        return 'Segera Diumumkan';
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    protected static function booted(): void
    {
        static::created(function (Event $event) {
            if ($event->jerseySizes()->count() === 0) {
                $standardSizes = [
                    ['size_name' => 'XS', 'gender_type' => 'unisex', 'chest_width_cm' => 46, 'body_length_cm' => 66, 'is_unlimited' => true, 'stock' => null],
                    ['size_name' => 'S', 'gender_type' => 'unisex', 'chest_width_cm' => 48, 'body_length_cm' => 68, 'is_unlimited' => true, 'stock' => null],
                    ['size_name' => 'M', 'gender_type' => 'unisex', 'chest_width_cm' => 50, 'body_length_cm' => 70, 'is_unlimited' => true, 'stock' => null],
                    ['size_name' => 'L', 'gender_type' => 'unisex', 'chest_width_cm' => 52, 'body_length_cm' => 72, 'is_unlimited' => true, 'stock' => null],
                    ['size_name' => 'XL', 'gender_type' => 'unisex', 'chest_width_cm' => 54, 'body_length_cm' => 74, 'is_unlimited' => true, 'stock' => null],
                    ['size_name' => 'XXL', 'gender_type' => 'unisex', 'chest_width_cm' => 56, 'body_length_cm' => 76, 'is_unlimited' => true, 'stock' => null],
                    ['size_name' => '3XL', 'gender_type' => 'unisex', 'chest_width_cm' => 58, 'body_length_cm' => 78, 'is_unlimited' => true, 'stock' => null],
                    ['size_name' => '4XL', 'gender_type' => 'unisex', 'chest_width_cm' => 60, 'body_length_cm' => 80, 'is_unlimited' => true, 'stock' => null],
                    ['size_name' => '5XL', 'gender_type' => 'unisex', 'chest_width_cm' => 62, 'body_length_cm' => 82, 'is_unlimited' => true, 'stock' => null],
                ];

                foreach ($standardSizes as $size) {
                    $event->jerseySizes()->create($size);
                }
            }
        });
    }

    public function categories(): HasMany
    {
        return $this->hasMany(EventCategory::class);
    }

    public function jerseySizes(): HasMany
    {
        return $this->hasMany(JerseySize::class)
            ->orderByRaw("CASE size_name 
                WHEN 'XS' THEN 1 
                WHEN 'S' THEN 2 
                WHEN 'M' THEN 3 
                WHEN 'L' THEN 4 
                WHEN 'XL' THEN 5 
                WHEN 'XXL' THEN 6 
                WHEN '2XL' THEN 6 
                WHEN '3XL' THEN 7 
                WHEN '4XL' THEN 8 
                WHEN '5XL' THEN 9 
                ELSE 10 
            END, chest_width_cm ASC");
    }

    public function customFields(): HasMany
    {
        return $this->hasMany(EventCustomField::class)->orderBy('sort_order');
    }

    public function addons(): HasMany
    {
        return $this->hasMany(EventAddon::class)->orderBy('sort_order')->orderBy('id');
    }

    public function promoCodes(): HasMany
    {
        return $this->hasMany(PromoCode::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function participants(): HasManyThrough
    {
        return $this->hasManyThrough(Participant::class, EventCategory::class);
    }

    public function isRegistrationOpen(): bool
    {
        $now = now();

        return $this->status === 'published' &&
            $now->between($this->registration_open_date, $this->registration_close_date);
    }
}
