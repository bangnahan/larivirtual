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
        Schema::create('activity_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->date('activity_date');
            $table->time('activity_time')->nullable();
            $table->decimal('distance_km', 8, 2);
            $table->unsignedInteger('duration_seconds');
            $table->decimal('calculated_pace', 6, 2)->nullable();
            $table->text('proof_url')->nullable();
            $table->string('proof_image_path')->nullable();
            $table->text('notes')->nullable();
            $table->string('validation_status', 30)->default('approved')->index();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_feedback')->nullable();
            $table->timestamps();

            $table->index(['participant_id', 'validation_status']);
            $table->index('activity_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_submissions');
    }
};
