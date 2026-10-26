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
            $columnsToDrop = [];
            $targets = [
                'is_vip',
                'is_custom_bib',
                'custom_bib_reason',
                'id_type',
                'id_number',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relation',
                'medical_conditions',
                'wave_group',
                'estimated_finish_time',
            ];

            foreach ($targets as $col) {
                if (Schema::hasColumn('participants', $col)) {
                    $columnsToDrop[] = $col;
                }
            }

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->boolean('is_vip')->default(false);
            $table->boolean('is_custom_bib')->default(false);
            $table->string('custom_bib_reason')->nullable();
            $table->string('id_type', 20)->default('KTP');
            $table->string('id_number')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->string('wave_group', 20)->nullable();
            $table->string('estimated_finish_time', 50)->nullable();
        });
    }
};
