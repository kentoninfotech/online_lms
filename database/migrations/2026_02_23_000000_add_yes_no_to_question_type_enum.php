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
        // SQLite and other databases don't support modifying enums directly,
        // so we need to use DB::statement for MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE quiz_questions MODIFY question_type ENUM('multiple_choice', 'true_false', 'short_answer', 'essay', 'yes_no') DEFAULT 'multiple_choice'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE quiz_questions MODIFY question_type ENUM('multiple_choice', 'true_false', 'short_answer', 'essay') DEFAULT 'multiple_choice'");
        }
    }
};
