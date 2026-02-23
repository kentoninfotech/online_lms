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
        Schema::create('course_bulk_message_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_bulk_message_id');
            $table->unsignedBigInteger('user_id');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('response')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('course_bulk_message_id')
                ->references('id')
                ->on('course_bulk_messages')
                ->onDelete('cascade');
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Indexes
            $table->index('course_bulk_message_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_bulk_message_recipients');
    }
};
