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
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id();
            
            // Section identifiers
            $table->string('section')->index(); // hero, about, features, testimonials, cta, contact, footer, etc.
            $table->string('key')->index(); // field name
            
            // Content storing
            $table->longText('value')->nullable(); // for text, descriptions
            $table->string('image_path')->nullable(); // for images
            $table->string('button_text')->nullable(); // for buttons
            $table->string('button_link')->nullable(); // for button links
            $table->string('title')->nullable(); // for titles/headings
            $table->text('description')->nullable(); // for descriptions
            
            // Metadata
            $table->string('data_type')->default('text'); // text, textarea, image, url, color, number
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            
            // Timestamps
            $table->timestamps();
            
            // Unique constraint for section + key
            $table->unique(['section', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_settings');
    }
};
