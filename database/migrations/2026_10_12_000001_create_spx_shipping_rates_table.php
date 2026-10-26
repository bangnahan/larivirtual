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
        Schema::create('spx_shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('origin_city', 100)->default('KAB. TANGERANG');
            $table->string('destination_city', 150);
            $table->string('destination_district', 150);
            $table->unsignedInteger('hemat_rate_per_kg')->default(0);
            $table->unsignedSmallInteger('hemat_sla_days')->default(0);
            $table->unsignedInteger('regular_rate_per_kg')->default(0);
            $table->unsignedSmallInteger('regular_sla_days')->default(0);
            $table->string('search_keyword')->index();
            $table->timestamps();

            $table->unique(['destination_city', 'destination_district']);
            $table->index('destination_city');
            $table->index('destination_district');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spx_shipping_rates');
    }
};
