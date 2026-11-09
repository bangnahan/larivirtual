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
            if (! Schema::hasColumn('events', 'organizer_name')) {
                $table->string('organizer_name')->nullable()->after('organizer_id');
            }
        });

        // Backfill existing events with their organizer name
        $events = DB::table('events')
            ->join('organizers', 'events.organizer_id', '=', 'organizers.id')
            ->select('events.id', 'organizers.name as org_name')
            ->get();

        foreach ($events as $event) {
            DB::table('events')
                ->where('id', $event->id)
                ->whereNull('organizer_name')
                ->update(['organizer_name' => $event->org_name]);
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'organizer_name')) {
                $table->dropColumn('organizer_name');
            }
        });
    }
};
