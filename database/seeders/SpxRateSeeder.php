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

        $gzPath = database_path('data/spx_rates.json.gz');
        if (! File::exists($gzPath)) {
            return;
        }

        $json = gzdecode(File::get($gzPath));
        $rows = json_decode($json, true);

        if (! is_array($rows)) {
            return;
        }

        $now = now()->toDateTimeString();
        $chunks = array_chunk($rows, 500);

        foreach ($chunks as $chunk) {
            foreach ($chunk as &$row) {
                $row['created_at'] = $now;
                $row['updated_at'] = $now;
            }
            unset($row);

            DB::table('spx_shipping_rates')->insertOrIgnore($chunk);
        }
    }
}
