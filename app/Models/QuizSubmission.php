<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizSubmission extends Model
{
    use HasFactory;

    protected $table = 'quiz_submissions';

    protected $fillable = [
        'course_enrollee_id',
        'quiz_id',
        'attempt_number',
        'total_questions',
        'correct_answers',
        'score',
        'is_passed',
        'time_taken_minutes',
        'submitted_at',
        'notes'
    ];

    protected $casts = [
        'is_passed' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the enrollment
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollee::class, 'course_enrollee_id');
    }

    /**
     * Get the quiz
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(CourseQuiz::class, 'quiz_id');
    }

    /**
     * Get all answer submissions
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuizSubmissionAnswer::class, 'submission_id');
    }

    /**
     * Check if this is the user's best score
     */
    public function isBestScore(): bool
    {
        $best = $this->enrollment
            ->quizSubmissions()
            ->where('quiz_id', $this->quiz_id)
            ->orderByDesc('score')
            ->first();

        return $best && $best->id === $this->id;
    }
}
