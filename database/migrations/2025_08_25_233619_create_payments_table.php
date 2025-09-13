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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade'); // parent who paid
            $table->decimal('amount', 10, 2);
            $table->string('file_path')->nullable(); // upload receipt
            $table->string('status')->default('pending'); // 'pending', 'verified', 'rejected'
            $table->text('decision_reason')->nullable(); // reason for approval/rejection
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
