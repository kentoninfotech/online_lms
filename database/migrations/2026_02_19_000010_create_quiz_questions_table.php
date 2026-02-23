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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('course_quizzes')->onDelete('cascade');
            $table->text('question');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'short_answer', 'essay', 'yes_no'])->default('multiple_choice');
            $table->text('correct_answer')->nullable(); // JSON for multiple options
            $table->integer('points')->default(1);
            $table->integer('sequence')->default(0);
            $table->timestamps();
            
            $table->index('quiz_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
