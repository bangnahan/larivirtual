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
        if (! Schema::hasColumn('jersey_sizes', 'surcharge')) {
            Schema::table('jersey_sizes', function (Blueprint $table) {
                $table->decimal('surcharge', 12, 2)->default(0)->after('body_length_cm');
            });
        }

        if (! Schema::hasColumn('participants', 'jersey_type')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->string('jersey_type', 20)->default('short')->after('jersey_size_id');
                $table->decimal('jersey_sleeve_surcharge', 12, 2)->default(0)->after('jersey_type');
                $table->decimal('jersey_size_surcharge', 12, 2)->default(0)->after('jersey_sleeve_surcharge');
                $table->decimal('jersey_surcharge', 12, 2)->default(0)->after('jersey_size_surcharge');
            });
        }

        if (! Schema::hasColumn('orders', 'jersey_surcharges_total')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('jersey_surcharges_total', 14, 2)->default(0)->after('subtotal');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'jersey_surcharges_total')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('jersey_surcharges_total');
            });
        }

        if (Schema::hasColumn('participants', 'jersey_type')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->dropColumn(['jersey_type', 'jersey_sleeve_surcharge', 'jersey_size_surcharge', 'jersey_surcharge']);
            });
        }

        if (Schema::hasColumn('jersey_sizes', 'surcharge')) {
            Schema::table('jersey_sizes', function (Blueprint $table) {
                $table->dropColumn('surcharge');
            });
        }
    }
};
