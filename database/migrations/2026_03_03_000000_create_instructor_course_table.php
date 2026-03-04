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
        Schema::create('instructor_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->enum('role', ['lead', 'co-instructor', 'assistant'])->default('lead');
            $table->text('bio')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('can_manage_content')->default(true);
            $table->boolean('can_manage_enrollees')->default(false);
            $table->boolean('can_manage_quizzes')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint to prevent duplicate assignments
            $table->unique(['instructor_id', 'course_id']);

            // Index for quick lookups
            $table->index(['instructor_id', 'is_active']);
            $table->index(['course_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_course');
    }
};
