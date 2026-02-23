<?php

namespace Database\Factories;

use App\Models\QuizQuestion;
use App\Models\CourseQuiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizQuestionFactory extends Factory
{
    protected $model = QuizQuestion::class;

    public function definition(): array
    {
        return [
            'quiz_id' => CourseQuiz::factory(),
            'question' => $this->faker->sentence() . '?',
            'question_type' => $this->faker->randomElement(['multiple_choice', 'true_false', 'short_answer']),
            'correct_answer' => ['A'],
            'points' => $this->faker->numberBetween(1, 5),
            'sequence' => $this->faker->numberBetween(1, 20),
        ];
    }
}
