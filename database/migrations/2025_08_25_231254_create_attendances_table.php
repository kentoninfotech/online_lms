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
            $table->morphs('attendable'); // creates attendable_id + attendable_type
            $table->timestamp('join_time')->nullable();
            $table->timestamp('leave_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('status')->default('absent'); // 'present', 'absent', 'late'
            $table->string('zoom_user_id')->nullable(); // from Zoom API
            $table->json('raw')->nullable(); // raw JSON data from Zoom
            $table->timestamps();

            $table->index(['lesson_occurrence_id', 'attendable_type', 'attendable_id'], 'attendance_attendable_index');
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
