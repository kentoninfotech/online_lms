<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseEnrollee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_enrollees';

    protected $fillable = [
        'user_id',
        'course_id',
        'course_date_id',
        'course_venue_id',
        'status',
        'payment_status',
        'amount_paid',
        'transaction_id',
        'payment_date',
        'enrolled_at',
        'completed_at',
        'progress_percentage',
        'notes'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'datetime',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user associated with this enrollment
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
     * Get the course date
     */
    public function courseDate(): BelongsTo
    {
        return $this->belongsTo(CourseDate::class, 'course_date_id');
    }

    /**
     * Get the venue
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(CourseVenue::class, 'course_venue_id');
    }

    /**
     * Get all content completions for this enrollment
     */
    public function contentCompletions(): HasMany
    {
        return $this->hasMany(CourseContentCompletion::class, 'course_enrollee_id');
    }

    /**
     * Get all quiz submissions
     */
    public function quizSubmissions(): HasMany
    {
        return $this->hasMany(QuizSubmission::class, 'course_enrollee_id');
    }

    /**
     * Get all payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(CoursePayment::class, 'course_enrollee_id');
    }

    /**
     * Get certificate if exists
     */
    public function certificate(): HasMany
    {
        return $this->hasMany(CourseCertificate::class, 'course_enrollee_id');
    }

    /**
     * Calculate course completion percentage including content and quizzes
     */
    public function calculateProgressPercentage(): int
    {
        // Get total required items (content + quizzes)
        $requiredContent = $this->course->contents()->where('is_required', true)->count();
        $requiredQuizzes = $this->course->quizzes()->where('is_required', true)->count();
        
        $totalRequired = $requiredContent + $requiredQuizzes;
        
        if ($totalRequired === 0) {
            return 0;
        }

        // Count completed content
        $completedContent = $this->contentCompletions()
            ->whereHas('content', fn($q) => $q->where('is_required', true))
            ->where('is_completed', true)
            ->count();

        // Count completed quizzes (submitted = completed)
        $completedQuizzes = $this->quizSubmissions()
            ->whereHas('quiz', fn($q) => $q->where('is_required', true))
            ->count();

        $totalCompleted = $completedContent + $completedQuizzes;

        return (int) (($totalCompleted / $totalRequired) * 100);
    }

    /**
     * Check if course is 100% complete
     */
    public function isCourseComplete(): bool
    {
        return $this->calculateProgressPercentage() === 100;
    }

    /**
     * Check if certificate already issued
     */
    public function hasCertificate(): bool
    {
        return $this->certificate()->exists();
    }

    /**
     * Get or create certificate when course is complete
     */
    public function generateCertificate(): ?CourseCertificate
    {
        // Only generate if course is complete and no certificate exists
        if (!$this->isCourseComplete() || $this->hasCertificate()) {
            return null;
        }

        $certificateNumber = 'CERT-' . strtoupper(uniqid($this->course->id . '-'));
        
        return $this->certificate()->create([
            'certificate_number' => $certificateNumber,
            'issued_at' => now(),
            'expires_at' => now()->addYear(), // Certificate valid for 1 year
        ]);
    }
}
