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
        Schema::create('course_enrollees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('course_date_id')->constrained('course_dates')->onDelete('cascade');
            $table->foreignId('course_venue_id')->constrained('course_venues')->onDelete('cascade');
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled', 'suspended'])->default('pending');
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('transaction_id')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->dateTime('enrolled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['user_id', 'course_id', 'course_date_id', 'course_venue_id'], 'unique_course_enrollment');
            $table->index('user_id');
            $table->index('course_id');
            $table->index('status');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollees');
    }
};
