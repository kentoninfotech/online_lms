<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_certificates';

    protected $fillable = [
        'course_enrollee_id',
        'certificate_number',
        'issued_at',
        'expires_at',
        'file_path',
        'is_revoked',
        'revoke_reason'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_revoked' => 'boolean',
    ];

    /**
     * Get the enrollment
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollee::class, 'course_enrollee_id');
    }

    /**
     * Check if certificate is valid
     */
    public function isValid(): bool
    {
        if ($this->is_revoked) {
            return false;
        }

        if ($this->expires_at && $this->expires_at < now()) {
            return false;
        }

        return true;
    }
}
