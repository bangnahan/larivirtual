<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpxShippingRate extends Model
{
    use HasFactory;

    protected $table = 'spx_shipping_rates';

    protected $fillable = [
        'origin_city',
        'destination_city',
        'destination_district',
        'hemat_rate_per_kg',
        'hemat_sla_days',
        'regular_rate_per_kg',
        'regular_sla_days',
        'search_keyword',
    ];

    /**
     * Scope pencarian cepat berdasarkan kata kunci wilayah
     */
    public function scopeSearch($query, string $keyword)
    {
        $normalized = strtolower(trim($keyword));

        return $query->where('search_keyword', 'like', "%{$normalized}%");
    }
}
