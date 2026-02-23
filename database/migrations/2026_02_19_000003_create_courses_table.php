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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('course_categories')->onDelete('cascade');
            $table->foreignId('facilitator_id')->nullable()->constrained('facilitators')->onDelete('set null');
            $table->decimal('fee', 10, 2);
            $table->string('featured_image')->nullable();
            $table->string('currency')->default('NGN');
            $table->integer('course_hours')->default(0);
            $table->boolean('is_online')->default(false);
            $table->boolean('is_offline')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('max_enrollees')->nullable();
            $table->integer('enrolled_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('category_id');
            $table->index('facilitator_id');
            $table->index('is_featured');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
