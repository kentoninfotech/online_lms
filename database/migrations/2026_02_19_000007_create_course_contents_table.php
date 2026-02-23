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
        Schema::create('course_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('content_type', ['text', 'pdf', 'excel', 'word', 'powerpoint', 'video', 'link', 'image'])->default('text');
            $table->longText('content')->nullable(); // HTML content for text
            $table->string('file_path')->nullable(); // Path to uploaded file
            $table->integer('duration_minutes')->nullable(); // Time required to read/watch (in minutes)
            $table->integer('sequence')->default(0);
            $table->integer('section_id')->nullable(); // Group contents into sections
            $table->boolean('is_published')->default(false);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('course_id');
            $table->index('sequence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_contents');
    }
};
