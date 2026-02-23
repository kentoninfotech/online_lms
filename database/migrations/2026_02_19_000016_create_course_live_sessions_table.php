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
        Schema::create('course_live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('facilitator_id')->constrained('facilitators')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end');
            $table->string('session_type')->default('zoom'); // zoom, meet, teams, etc.
            $table->string('meeting_link')->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
            $table->integer('attendees_count')->default(0);
            $table->dateTime('actual_start')->nullable();
            $table->dateTime('actual_end')->nullable();
            $table->text('recording_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('course_id');
            $table->index('facilitator_id');
            $table->index('status');
            $table->index('scheduled_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_live_sessions');
    }
};
