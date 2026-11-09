<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
        });

        if (DB::table('events')->where('slug', 'bhinneka')->exists()) {
            DB::table('events')->where('slug', 'bhinneka')->update(['is_featured' => true]);
        } elseif (DB::table('events')->where('status', 'published')->exists()) {
            DB::table('events')->where('status', 'published')->limit(1)->update(['is_featured' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
        });
    }
};
