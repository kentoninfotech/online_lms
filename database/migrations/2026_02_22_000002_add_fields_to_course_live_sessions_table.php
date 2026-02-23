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
        Schema::table('course_live_sessions', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('course_live_sessions', 'is_compulsory')) {
                $table->boolean('is_compulsory')->default(false)->after('recording_url');
            }
            if (!Schema::hasColumn('course_live_sessions', 'duration_minutes')) {
                $table->integer('duration_minutes')->nullable()->after('is_compulsory');
            }
            if (!Schema::hasColumn('course_live_sessions', 'max_points')) {
                $table->integer('max_points')->default(0)->after('duration_minutes');
            }
            if (!Schema::hasColumn('course_live_sessions', 'jitsi_room_name')) {
                $table->string('jitsi_room_name')->nullable()->unique()->after('max_points');
            }
            if (!Schema::hasColumn('course_live_sessions', 'chat_enabled')) {
                $table->boolean('chat_enabled')->default(true)->after('jitsi_room_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_live_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'is_compulsory',
                'duration_minutes',
                'max_points',
                'jitsi_room_name',
                'chat_enabled'
            ]);
        });
    }
};
