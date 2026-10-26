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
        Schema::table('participants', function (Blueprint $table) {
            if (! Schema::hasColumn('participants', 'bib_image_path')) {
                $table->string('bib_image_path')->nullable()->after('bib_name');
            }
            if (! Schema::hasColumn('participants', 'finisher_status')) {
                $table->string('finisher_status', 30)->default('in_progress')->after('qr_token')->index();
            }
            if (! Schema::hasColumn('participants', 'total_distance_km')) {
                $table->decimal('total_distance_km', 8, 2)->default(0.00)->after('finisher_status');
            }
            if (! Schema::hasColumn('participants', 'total_duration_seconds')) {
                $table->unsignedInteger('total_duration_seconds')->default(0)->after('total_distance_km');
            }
            if (! Schema::hasColumn('participants', 'finished_at')) {
                $table->dateTime('finished_at')->nullable()->after('total_duration_seconds');
            }
            if (! Schema::hasColumn('participants', 'certificate_path')) {
                $table->string('certificate_path')->nullable()->after('finished_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn([
                'bib_image_path',
                'finisher_status',
                'total_distance_km',
                'total_duration_seconds',
                'finished_at',
                'certificate_path',
            ]);
        });
    }
};
