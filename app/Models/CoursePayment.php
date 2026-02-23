<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoursePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_payments';

    protected $fillable = [
        'course_enrollee_id',
        'user_id',
        'course_id',
        'amount',
        'currency',
        'payment_method',
        'reference_id',
        'status',
        'approval_status',
        'payment_evidence_path',
        'payment_evidence_amount',
        'payer_name',
        'approval_notes',
        'approved_by',
        'approved_at',
        'payment_details',
        'paid_at',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_evidence_amount' => 'decimal:2',
        'payment_details' => 'json',
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the enrollment
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollee::class, 'course_enrollee_id');
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the admin who approved this payment
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
