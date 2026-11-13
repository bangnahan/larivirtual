<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'addons_total' => 'decimal:2',
        'jersey_surcharges_total' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'payment_gateway_fee' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'meta_capi_purchase_sent_at' => 'datetime',
    ];

    public function isMetaCapiPurchaseSent(): bool
    {
        return ! is_null($this->meta_capi_purchase_sent_at);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(OrderAddon::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function shippingAddress(): HasOne
    {
        return $this->hasOne(OrderShippingAddress::class);
    }

    public function paymentLogs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'Lunas (Paid)',
            'pending' => 'Menunggu Pembayaran',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Pengembalian Dana (Refund)',
            'expired' => 'Kadaluarsa',
            'failed' => 'Gagal',
            default => ucfirst($this->status ?? '-'),
        };
    }

    /**
     * Dapatkan ringkasan rincian isi paket untuk label pengiriman SPX & packing tim logistik
     */
    public function getPackageContentsSummary(): string
    {
        $parts = [];

        $this->loadMissing(['participants.category', 'participants.jerseySize', 'addons']);

        $ticketCount = $this->participants->count();
        if ($ticketCount > 0) {
            $jerseyList = $this->participants->map(function ($p) {
                $cat = $p->category?->name ?? 'Tiket';
                $size = $p->jerseySize?->size_name ?? '-';

                return "{$cat}({$size})";
            })->implode(', ');

            $parts[] = "{$ticketCount}x Race Pack [{$jerseyList}]";
        }

        if ($this->addons && $this->addons->isNotEmpty()) {
            $addonList = $this->addons->map(function ($a) {
                return "{$a->quantity}x {$a->addon_name}";
            })->implode(', ');

            $parts[] = "Add-on: {$addonList}";
        }

        return ! empty($parts) ? implode(' + ', $parts) : 'Paket Race Pack Virtual Run';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->isPending() && $this->expired_at && now()->greaterThan($this->expired_at));
    }
}
