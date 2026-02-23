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
        // Update all courses that don't have a level set to 'Local'
        DB::table('courses')
            ->whereNull('level')
            ->orWhere('level', '')
            ->update(['level' => 'Local']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a data migration, reversing it would lose data
        // so we'll keep it as is
    }
};
