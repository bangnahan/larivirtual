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
            if (! Schema::hasColumn('events', 'event_code')) {
                $table->string('event_code', 20)->nullable()->after('slug');
            }
            if (! Schema::hasColumn('events', 'activity_type')) {
                $table->string('activity_type', 20)->default('RUN')->after('event_code');
            }
            if (! Schema::hasColumn('events', 'submission_mode')) {
                $table->string('submission_mode', 20)->default('CUMULATIVE')->after('activity_type');
            }
            if (! Schema::hasColumn('events', 'race_start_date')) {
                $table->dateTime('race_start_date')->nullable()->after('registration_close_date');
            }
            if (! Schema::hasColumn('events', 'race_end_date')) {
                $table->dateTime('race_end_date')->nullable()->after('race_start_date');
            }
        });

        Schema::table('event_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('event_categories', 'base_weight_grams')) {
                $table->unsignedInteger('base_weight_grams')->default(500)->after('normal_price');
            }
            $table->string('bib_prefix', 20)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'event_code',
                'activity_type',
                'submission_mode',
                'race_start_date',
                'race_end_date',
            ]);
        });

        Schema::table('event_categories', function (Blueprint $table) {
            $table->dropColumn('base_weight_grams');
        });
    }
};
