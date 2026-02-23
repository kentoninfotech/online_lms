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
        Schema::create('live_session_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_session_id')->constrained('course_live_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('joined_at')->nullable();
            $table->dateTime('left_at')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->enum('attendance_status', ['present', 'absent', 'late', 'partial'])->default('present');
            $table->timestamps();
            
            $table->unique(['live_session_id', 'user_id']);
            $table->index('live_session_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_session_attendances');
    }
};
