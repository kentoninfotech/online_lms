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
        Schema::create('course_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('total_questions')->default(0);
            $table->integer('passing_score')->default(50); // Percentage
            $table->integer('time_limit_minutes')->nullable();
            $table->integer('attempts_allowed')->default(3);
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('is_published')->default(false);
            $table->integer('sequence')->default(0);
            $table->boolean('is_required')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_quizzes');
    }
};
