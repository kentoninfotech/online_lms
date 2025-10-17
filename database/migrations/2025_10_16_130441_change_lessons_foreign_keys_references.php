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
        Schema::table('lessons', function (Blueprint $table) {
            // Drop existing foreign key constraints if they exist
            if (Schema::hasColumn('lessons', 'instructor_id')) {
                $table->dropForeign(['instructor_id']);
            }
            if (Schema::hasColumn('lessons', 'student_id')) {
                $table->dropForeign(['student_id']);
            }

            // instructor_id should reference 'instructors' table
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');

            // student_id should reference 'students' table
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only attempt to drop/modify the table if it exists
        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) {
                // Drop modified foreign key constraints if they exist
                if (Schema::hasColumn('lessons', 'instructor_id')) {
                    $table->dropForeign(['instructor_id']);
                }
                if (Schema::hasColumn('lessons', 'student_id')) {
                    $table->dropForeign(['student_id']);
                }

                // Revert back to original foreign key constraints referencing 'users' table
                $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }
};
