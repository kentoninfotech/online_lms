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
        // Add profile column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile')->nullable()->after('user_type');
        });

        // Drop profile column 
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('profile');
        });

        // Drop profile column 
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropColumn('profile');
        });

        // Drop profile column 
        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn('profile');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile');
        });

        // Add Column on rollback
        Schema::table('students', function (Blueprint $table) {
            $table->string('profile')->nullable()->after('email');
        });

        Schema::table('instructors', function (Blueprint $table) {
            $table->string('profile')->nullable()->after('email');
        });

        Schema::table('parents', function (Blueprint $table) {
            $table->string('profile')->nullable()->after('email');
        });
    }
};
