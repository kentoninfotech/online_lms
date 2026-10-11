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
        // Drop existing tables if they exist
        Schema::dropIfExists('course_lesson_attendees');
        Schema::dropIfExists('course_lessons');

        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->string('topic');
            $table->text('description')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('capacity')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('course_id');
            $table->index('instructor_id');
            $table->index('start_time');
            $table->index('is_published');
        });

        Schema::create('course_lesson_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_lesson_id')->constrained('course_lessons')->onDelete('cascade');
            $table->foreignId('course_enrollee_id')->constrained('course_enrollees')->onDelete('cascade');
            $table->dateTime('attended_at')->nullable();
            $table->enum('status', ['registered', 'attended', 'absent', 'cancelled'])->default('registered');
            $table->text('feedback')->nullable();
            $table->timestamps();
            
            $table->unique(['course_lesson_id', 'course_enrollee_id'], 'lesson_enrollee_unique');
            $table->index('course_lesson_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lesson_attendees');
        Schema::dropIfExists('course_lessons');
    }
};
