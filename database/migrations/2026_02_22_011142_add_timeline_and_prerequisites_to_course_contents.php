<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_contents', function (Blueprint $table) {
            // Timeline for content availability
            $table->datetime('available_from')->nullable()->comment('Date/time when content becomes available to students');
            $table->datetime('available_until')->nullable()->comment('Date/time until when content is available');
            
            // Prerequisites
            $table->unsignedBigInteger('prerequisite_content_id')->nullable()->comment('Required content to complete before this one');
            $table->foreign('prerequisite_content_id')->references('id')->on('course_contents')->onDelete('set null');
            
            // Tracking and metadata
            $table->integer('min_reading_time_minutes')->default(0)->comment('Minimum time student should spend on this content');
            $table->string('embed_type')->default('default')->comment('How content is embedded: default, iframe, popup, fullscreen, modal');
            $table->boolean('allow_download')->default(true)->comment('Allow students to download content');
            $table->boolean('track_viewing')->default(true)->comment('Track when students view this content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_contents', function (Blueprint $table) {
            $table->dropForeign(['prerequisite_content_id']);
            $table->dropColumn(['available_from', 'available_until', 'prerequisite_content_id', 'min_reading_time_minutes', 'embed_type', 'allow_download', 'track_viewing']);
        });
    }
};
