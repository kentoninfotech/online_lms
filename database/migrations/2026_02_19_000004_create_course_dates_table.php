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
        Schema::create('course_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('date_label')->nullable(); // E.g., "13 - 17 Apr., 2026"
            $table->integer('sequence')->default(1); // 1st date, 2nd date, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('course_id');
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_dates');
    }
};
