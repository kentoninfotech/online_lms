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
        Schema::table('quiz_answers', function (Blueprint $table) {
            // Make 'answer' column nullable with a default value
            // This fixes the "Field 'answer' doesn't have a default value" error
            $table->string('answer')->nullable()->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_answers', function (Blueprint $table) {
            // Revert changes
            $table->string('answer')->nullable(false)->default(null)->change();
        });
    }
};
