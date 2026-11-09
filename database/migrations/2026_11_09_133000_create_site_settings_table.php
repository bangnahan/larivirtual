<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        $defaults = [
            'top_bar_enabled' => '1',
            'top_bar_badge' => 'LIVE',
            'top_bar_message' => 'Pendaftaran Bhinneka Virtual Run 42K Masih Dibuka! Lari bisa dicicil s.d 31 Des 2026.',
            'top_bar_button_text' => 'Cek Nomor E-BIB Anda',
            'top_bar_button_url' => '/ticket',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
