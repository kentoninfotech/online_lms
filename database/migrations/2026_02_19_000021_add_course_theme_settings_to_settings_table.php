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
        // Add theme color settings columns to existing settings table
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'primary_color')) {
                $table->string('primary_color')->default('#3B82F6')->nullable(); // Blue
            }
            if (!Schema::hasColumn('settings', 'secondary_color')) {
                $table->string('secondary_color')->default('#1E40AF')->nullable(); // Darker blue
            }
            if (!Schema::hasColumn('settings', 'accent_color')) {
                $table->string('accent_color')->default('#60A5FA')->nullable(); // Light blue
            }
            if (!Schema::hasColumn('settings', 'courses_enabled')) {
                $table->boolean('courses_enabled')->default(true);
            }
            if (!Schema::hasColumn('settings', 'online_courses_enabled')) {
                $table->boolean('online_courses_enabled')->default(true);
            }
            if (!Schema::hasColumn('settings', 'featured_courses_limit')) {
                $table->integer('featured_courses_limit')->default(6);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color',
                'secondary_color',
                'accent_color',
                'courses_enabled',
                'online_courses_enabled',
                'featured_courses_limit'
            ]);
        });
    }
};
