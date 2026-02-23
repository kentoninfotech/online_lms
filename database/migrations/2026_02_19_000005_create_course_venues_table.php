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
        Schema::create('course_venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_date_id')->constrained('course_dates')->onDelete('cascade');
            $table->string('venue_name'); // E.g., "Lagos"
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('Nigeria');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('enrolled_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('course_date_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_venues');
    }
};
