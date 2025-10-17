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
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'student_id')) {
                // Drop the existing foreign key constraint
                $table->dropForeign(['student_id']);
            }

            // Add new foreign key constraint referencing 'students' table
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only attempt to drop/modify the table if it exists
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('subscriptions', 'student_id')) {
                    // Drop the modified foreign key constraint
                    $table->dropForeign(['student_id']);
                }

                // Revert back to original foreign key constraint referencing 'users' table
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }
};
