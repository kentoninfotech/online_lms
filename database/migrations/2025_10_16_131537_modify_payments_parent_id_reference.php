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
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'parent_id')) {
                // Drop the existing foreign key constraint
                $table->dropForeign(['parent_id']);
            }

            // Add new foreign key constraint referencing 'parents' table
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (Schema::hasColumn('payments', 'parent_id')) {
                    // Drop the modified foreign key constraint
                    $table->dropForeign(['parent_id']);
                }

                // Revert back to original foreign key constraint referencing 'users' table
                $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }
};
