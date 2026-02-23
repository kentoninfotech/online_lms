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
        Schema::create('course_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_enrollee_id')->constrained('course_enrollees')->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->dateTime('issued_at');
            $table->dateTime('expires_at')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_revoked')->default(false);
            $table->text('revoke_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('course_enrollee_id');
            $table->index('is_revoked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_certificates');
    }
};
