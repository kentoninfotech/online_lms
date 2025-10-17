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
        if (Schema::hasTable('parent_student')) {
            Schema::table('parent_student', function (Blueprint $table) {
                // Drop existing foreign key constraints if they exist
                if (Schema::hasColumn('parent_student', 'parent_id')){
                    $table->dropForeign(['parent_id']);
                }
                if (Schema::hasColumn('parent_student', 'student_id')){
                    $table->dropForeign(['student_id']);
                }

                // Add new foreign key constraints referencing 'parents' and 'students' tables
                $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parent_student', function (Blueprint $table) {
            // Drop the modified foreign key constraints
            if (Schema::hasColumn('parent_student', 'parent_id')){
                $table->dropForeign(['parent_id']);
            }
            if (Schema::hasColumn('parent_student', 'student_id')){
                $table->dropForeign(['student_id']);
            }

            // Revert back to original foreign key constraints referencing 'users' table
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
