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
        Schema::create('contact_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_message_id');
            $table->unsignedBigInteger('admin_id');
            $table->longText('response_text');
            $table->dateTime('responded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('contact_message_id')
                ->references('id')
                ->on('contact_messages')
                ->onDelete('cascade');

            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Indexes
            $table->index('contact_message_id');
            $table->index('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_responses');
    }
};
