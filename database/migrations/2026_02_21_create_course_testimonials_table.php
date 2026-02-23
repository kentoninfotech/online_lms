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
        Schema::create('course_testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('enrollee_id')->nullable()->constrained('course_enrollees')->onDelete('set null');
            $table->tinyInteger('rating')->unsigned()->comment('1-5 star rating');
            $table->string('title')->nullable()->comment('Testimonial title');
            $table->longText('content')->comment('Testimonial text');
            $table->boolean('is_approved')->default(false)->comment('Admin approval status');
            $table->boolean('is_featured')->default(false)->comment('Display on course page');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index for finding approved testimonials
            $table->index(['course_id', 'is_approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_testimonials');
    }
};
