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
            $table->string('meta_pixel_id')->nullable()->after('waiver_content');
            $table->text('meta_capi_access_token')->nullable()->after('meta_pixel_id');
            $table->string('meta_capi_test_event_code')->nullable()->after('meta_capi_access_token');
            $table->boolean('is_meta_capi_enabled')->default(true)->after('meta_capi_test_event_code');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('status');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('fbp')->nullable()->after('user_agent');
            $table->string('fbc')->nullable()->after('fbp');
            $table->dateTime('meta_capi_purchase_sent_at')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'ip_address',
                'user_agent',
                'fbp',
                'fbc',
                'meta_capi_purchase_sent_at',
            ]);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'meta_pixel_id',
                'meta_capi_access_token',
                'meta_capi_test_event_code',
                'is_meta_capi_enabled',
            ]);
        });
    }
};
