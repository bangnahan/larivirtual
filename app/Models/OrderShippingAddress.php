<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'recipient_name',
        'recipient_phone',
        'province',
        'destination_city',
        'destination_district',
        'postal_code',
        'address_detail',
        'total_weight_grams',
        'courier_name',
        'service_type',
        'shipping_cost',
        'estimated_sla_days',
        'tracking_number',
        'shipping_status',
        'shipped_at',
        'admin_shipping_notes',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'total_weight_grams' => 'integer',
        'estimated_sla_days' => 'integer',
        'shipped_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
