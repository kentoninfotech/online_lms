<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::table('course_enrollees', function (Blueprint $table) {
            $table->unsignedBigInteger('course_date_id')->nullable()->change();
        });

        Schema::table('course_enrollees', function (Blueprint $table) {
            $table->unsignedBigInteger('course_venue_id')->nullable()->change();
        });

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do nothing - we don't need to revert this
    }
};
