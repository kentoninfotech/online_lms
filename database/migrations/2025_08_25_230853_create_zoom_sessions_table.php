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
        Schema::create('zoom_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_occurrence_id')->constrained('lesson_occurrences')->onDelete('cascade');
            $table->string('zoom_meeting_id')->nullable();
            $table->string('topic')->nullable();
            $table->string('join_url')->nullable();
            $table->string('start_url')->nullable();
            $table->enum('status', ['scheduled', 'started', 'ended', 'cancelled'])->default('scheduled');
            $table->json('raw')->nullable(); // store Zoom API JSON payload
            $table->timestamps();

            $table->unique('lesson_occurrence_id'); // one session per occurrence
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_sessions');
    }
};
