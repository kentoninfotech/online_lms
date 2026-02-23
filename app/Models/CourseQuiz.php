<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseQuiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_quizzes';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'total_questions',
        'passing_score',
        'time_limit_minutes',
        'attempts_allowed',
        'show_correct_answers',
        'shuffle_questions',
        'is_published',
        'sequence',
        'is_required'
    ];

    protected $casts = [
        'show_correct_answers' => 'boolean',
        'shuffle_questions' => 'boolean',
        'is_published' => 'boolean',
        'is_required' => 'boolean',
    ];

    /**
     * Get the course this quiz belongs to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get all questions
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id')
            ->orderBy('sequence');
    }

    /**
     * Get all submissions
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(QuizSubmission::class, 'quiz_id');
    }

    /**
     * Get top score for a user
     */
    public function getTopScore($userId)
    {
        return $this->submissions()
            ->whereHas('enrollment', fn($q) => $q->where('user_id', $userId))
            ->orderByDesc('score')
            ->first();
    }
}
