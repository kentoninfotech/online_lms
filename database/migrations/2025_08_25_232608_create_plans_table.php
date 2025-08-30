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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('duration_type'); // e.g. daily, weekly, monthly
            $table->integer('duration_count')->default(1); // e.g., 1 week, 1 month
        
            // Overrideable rules (null = use global settings)
            $table->integer('reschedule_limit')->nullable(); // max reschedules in billing cycle
            $table->integer('payment_grace_days')->nullable(); // days before auto-suspension
            
            $table->text('features')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
