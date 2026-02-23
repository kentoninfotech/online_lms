<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizSubmissionAnswer extends Model
{
    use HasFactory;

    protected $table = 'quiz_submission_answers';

    protected $fillable = [
        'submission_id',
        'question_id',
        'user_answer',
        'is_correct',
        'points_earned'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Get the submission
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(QuizSubmission::class, 'submission_id');
    }

    /**
     * Get the question
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
