<?php

namespace App\Services;

use App\Models\SpxShippingRate;
use Exception;

class SpxShippingService
{
    /**
     * Cari wilayah tujuan pengiriman untuk autocomplete (Kota & Kecamatan)
     */
    public function searchDestination(string $keyword, int $limit = 20): array
    {
        $normalized = trim($keyword);
        if (strlen($normalized) < 2) {
            return [];
        }

        return SpxShippingRate::search($normalized)
            ->limit($limit)
            ->get()
            ->map(function (SpxShippingRate $rate) {
                return [
                    'id' => $rate->id,
                    'destination_city' => $rate->destination_city,
                    'destination_district' => $rate->destination_district,
                    'rate_per_kg' => (int) $rate->regular_rate_per_kg,
                    'sla_days' => (int) $rate->regular_sla_days,
                    'label' => "{$rate->destination_city} - {$rate->destination_district}",
                ];
            })
            ->toArray();
    }

    /**
     * Hitung ongkos kirim SPX Regular berdasarkan kota, kecamatan, dan bobot total (gram)
     */
    public function calculateShipping(
        string $destinationCity,
        string $destinationDistrict,
        int $totalWeightGrams = 500
    ): array {
        $city = strtoupper(trim($destinationCity));
        $district = strtoupper(trim($destinationDistrict));

        $rate = SpxShippingRate::where('destination_city', $city)
            ->where('destination_district', $district)
            ->first();

        if (! $rate) {
            // Pencarian alternatif fleksibel jika nama kabupaten/kecamatan sedikit berbeda spasi/karakter
            $rate = SpxShippingRate::where('destination_district', $district)
                ->where('destination_city', 'like', "%{$city}%")
                ->first();
        }

        if (! $rate) {
            throw new Exception("Tarif pengiriman SPX untuk tujuan {$city} - {$district} tidak ditemukan.");
        }

        // Bobot minimal 1 kg (1000 gram), pembulatan SPX ke kg berikutnya
        $weightInKg = max(1, (int) ceil($totalWeightGrams / 1000));
        $ratePerKg = (int) $rate->regular_rate_per_kg;
        $totalCost = $weightInKg * $ratePerKg;

        return [
            'courier' => 'SPX',
            'service' => 'SPX Regular',
            'origin' => $rate->origin_city ?: 'KAB. TANGERANG',
            'destination_city' => $rate->destination_city,
            'destination_district' => $rate->destination_district,
            'total_weight_grams' => $totalWeightGrams,
            'billed_weight_kg' => $weightInKg,
            'rate_per_kg' => $ratePerKg,
            'shipping_cost' => $totalCost,
            'sla_days' => (int) $rate->regular_sla_days,
        ];
    }
}
