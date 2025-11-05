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
        Schema::create('bulk_message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulk_message_id')->constrained('bulk_messages')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('delivery_method', ['email', 'sms']);
            $table->enum('delivery_status', ['queued', 'sent', 'failed'])->default('queued');
            $table->text('response_log')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_message_recipients');
    }
};
