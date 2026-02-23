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
        Schema::create('course_content_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_enrollee_id')->constrained('course_enrollees')->onDelete('cascade');
            $table->foreignId('course_content_id')->constrained('course_contents')->onDelete('cascade');
            $table->integer('time_spent_minutes')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['course_enrollee_id', 'course_content_id'], 'ccc_enrollee_content_unique');
            $table->index('course_enrollee_id');
            $table->index('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_content_completions');
    }
};
