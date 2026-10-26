<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SpxRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = DB::table('spx_shipping_rates')->count();
        if ($count >= 7000) {
            return;
        }

        $sourceDb = '/Users/bangnahan/Ongkir SPX/api/database/spx_shipping.sqlite';
        if (File::exists($sourceDb) && config('database.default') === 'sqlite') {
            DB::statement("ATTACH '{$sourceDb}' AS spx_source");
            DB::statement('INSERT OR IGNORE INTO spx_shipping_rates (
                origin_city, destination_city, destination_district,
                hemat_rate_per_kg, hemat_sla_days, regular_rate_per_kg,
                regular_sla_days, search_keyword, created_at, updated_at
            ) SELECT 
                origin_city, destination_city, destination_district,
                hemat_rate_per_kg, hemat_sla_days, regular_rate_per_kg,
                regular_sla_days, search_keyword, created_at, updated_at
            FROM spx_source.spx_rates');
            DB::statement('DETACH spx_source');
        }
    }
}
