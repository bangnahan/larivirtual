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
        if (! Schema::hasColumn('orders', 'shipping_cost')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('shipping_cost', 12, 2)->default(0)->after('subtotal');
            });
        }

        Schema::create('order_shipping_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            $table->string('recipient_name');
            $table->string('recipient_phone', 30);
            $table->string('province', 100)->nullable();
            $table->string('destination_city', 150);
            $table->string('destination_district', 150);
            $table->string('postal_code', 10)->nullable();
            $table->text('address_detail');
            $table->unsignedInteger('total_weight_grams')->default(500);
            $table->string('courier_name', 50)->default('SPX');
            $table->string('service_type', 50)->default('Regular');
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->unsignedSmallInteger('estimated_sla_days')->default(0);
            $table->string('tracking_number', 100)->nullable()->index();
            $table->string('shipping_status', 30)->default('pending')->index();
            $table->dateTime('shipped_at')->nullable();
            $table->text('admin_shipping_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shipping_addresses');

        if (Schema::hasColumn('orders', 'shipping_cost')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('shipping_cost');
            });
        }
    }
};
