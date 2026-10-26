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
        Schema::create('event_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('image_path')->nullable();
            $table->integer('weight_grams')->default(150);
            $table->boolean('is_unlimited')->default(true);
            $table->integer('stock')->nullable();
            $table->integer('allocated_stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('order_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('event_addon_id')->nullable()->constrained('event_addons')->nullOnDelete();
            $table->string('addon_name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
        });

        if (! Schema::hasColumn('orders', 'addons_total')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('addons_total', 14, 2)->default(0)->after('subtotal');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'addons_total')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('addons_total');
            });
        }
        Schema::dropIfExists('order_addons');
        Schema::dropIfExists('event_addons');
    }
};
