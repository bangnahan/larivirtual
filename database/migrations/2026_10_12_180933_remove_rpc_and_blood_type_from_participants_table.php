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
            try {
                $table->dropForeign(['rpc_claimed_by_user_id']);
            } catch (Throwable $e) {
                // Ignore if foreign key doesn't exist
            }
        });

        Schema::table('participants', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['blood_type', 'is_rpc_claimed', 'rpc_claimed_at', 'rpc_claimed_by_user_id', 'is_proxy_claimed', 'proxy_collector_name', 'proxy_collector_id_number'] as $column) {
                if (Schema::hasColumn('participants', $column)) {
                    $columnsToDrop[] = $column;
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
            $table->string('blood_type', 10)->nullable();
            $table->boolean('is_rpc_claimed')->default(false);
            $table->dateTime('rpc_claimed_at')->nullable();
            $table->unsignedBigInteger('rpc_claimed_by_user_id')->nullable();
        });
    }
};
