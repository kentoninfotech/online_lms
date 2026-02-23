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
        Schema::table('course_payments', function (Blueprint $table) {
            // Add payment approval fields
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            $table->string('payment_evidence_path')->nullable()->after('approval_status');
            $table->decimal('payment_evidence_amount', 12, 2)->nullable()->after('payment_evidence_path');
            $table->string('payer_name')->nullable()->after('payment_evidence_amount');
            $table->text('approval_notes')->nullable()->after('payer_name');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('approval_notes');
            $table->dateTime('approved_at')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_payments', function (Blueprint $table) {
            $table->dropColumn([
                'approval_status',
                'payment_evidence_path',
                'payment_evidence_amount',
                'payer_name',
                'approval_notes',
                'approved_by',
                'approved_at'
            ]);
        });
    }
};
