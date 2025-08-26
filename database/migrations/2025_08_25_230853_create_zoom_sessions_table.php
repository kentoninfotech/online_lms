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
            $table->timestamps();
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
