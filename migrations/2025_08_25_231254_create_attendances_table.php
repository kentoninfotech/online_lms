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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_occurrence_id')->constrained('lesson_occurrences')->onDelete('cascade');

            $table->nullableMorphs('attendable'); // attendable_id + attendable_type (nullable)
            $table->timestamp('join_time')->nullable();
            $table->timestamp('leave_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('status')->default('absent'); // 'present', 'absent', 'late'
            $table->string('zoom_user_id')->nullable(); // from Zoom API
            $table->timestamps();

            $table->unique(['lesson_occurrence_id', 'attendable_type', 'attendable_id', 'zoom_user_id'], 'attendance_unique');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
