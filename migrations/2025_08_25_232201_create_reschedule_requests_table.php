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
        Schema::create('reschedule_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_occurrence_id')->constrained('lesson_occurrences')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade'); // parent or student
            $table->dateTime('proposed_start');
            $table->string('status')->default('pending');  // 'pending', 'approved', 'rejected'
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reschedule_requests');
    }
};
