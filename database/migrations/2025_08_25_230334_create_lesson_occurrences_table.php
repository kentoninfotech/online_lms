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
        Schema::create('lesson_occurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->datetime('scheduled_start');
            $table->integer('duration_minutes');
            $table->string('status')->default('scheduled'); //'scheduled', 'completed', 'cancelled'
            $table->timestamps();
            
            $table->index(['lesson_id', 'scheduled_start']);
            
            $table->unique(['lesson_id', 'scheduled_start'], 'lesson_occurrence_unique');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_occurrences');
    }
};
