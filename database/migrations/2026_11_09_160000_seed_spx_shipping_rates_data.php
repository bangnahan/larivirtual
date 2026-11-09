<?php

use Database\Seeders\SpxRateSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        (new SpxRateSeeder)->run();
    }

    public function down(): void
    {
        // No need to truncate in down
    }
};
