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
        Schema::create('course_facilitator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('facilitator_id')->constrained('facilitators')->onDelete('cascade');
            $table->integer('order')->default(0)->comment('Display order of facilitator');
            $table->text('bio')->nullable()->comment('Brief bio/info about facilitator for this course');
            $table->timestamps();

            // Ensure one facilitator per course only appears once
            $table->unique(['course_id', 'facilitator_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_facilitator');
    }
};
