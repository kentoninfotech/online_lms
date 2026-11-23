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
        Schema::create('course_content_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_content_id')->constrained('course_contents')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path'); // Path to uploaded file
            $table->string('file_type'); // Extension: pdf, doc, etc.
            $table->bigInteger('file_size'); // Size in bytes
            $table->string('mime_type')->nullable(); // MIME type
            $table->integer('sequence')->default(0); // Display order
            $table->boolean('is_published')->default(true);
            $table->unsignedBigInteger('download_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('course_content_id');
            $table->index('sequence');
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_content_resources');
    }
};
