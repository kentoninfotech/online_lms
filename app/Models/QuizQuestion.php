<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $table = 'quiz_questions';

    protected $fillable = [
        'quiz_id',
        'question',
        'question_type',
        'correct_answer',
        'points',
        'sequence'
    ];

    protected $casts = [
        'correct_answer' => 'json',
    ];

    /**
     * Get the quiz this question belongs to
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(CourseQuiz::class, 'quiz_id');
    }

    /**
     * Get all answer options
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'question_id')
            ->orderBy('sequence');
    }
}
