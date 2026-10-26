<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'racepack_delivery_start_date')) {
                $table->dateTime('racepack_delivery_start_date')->nullable()->after('race_end_date');
            }
            if (! Schema::hasColumn('events', 'racepack_delivery_end_date')) {
                $table->dateTime('racepack_delivery_end_date')->nullable()->after('racepack_delivery_start_date');
            }
            if (! Schema::hasColumn('events', 'racepack_delivery_schedule')) {
                $table->string('racepack_delivery_schedule', 100)->nullable()->after('racepack_delivery_end_date');
            }
            if (! Schema::hasColumn('events', 'racepack_delivery_notes')) {
                $table->text('racepack_delivery_notes')->nullable()->after('racepack_delivery_schedule');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'racepack_delivery_start_date',
                'racepack_delivery_end_date',
                'racepack_delivery_schedule',
                'racepack_delivery_notes',
            ]);
        });
    }
};
