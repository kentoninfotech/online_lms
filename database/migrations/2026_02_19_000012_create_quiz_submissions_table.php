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
        Schema::create('quiz_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_enrollee_id')->constrained('course_enrollees')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('course_quizzes')->onDelete('cascade');
            $table->integer('attempt_number')->default(1);
            $table->integer('total_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('score')->default(0); // Percentage
            $table->boolean('is_passed')->default(false);
            $table->integer('time_taken_minutes')->nullable();
            $table->dateTime('submitted_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('course_enrollee_id');
            $table->index('quiz_id');
            $table->index('is_passed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_submissions');
    }
};
