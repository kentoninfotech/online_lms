<?php

namespace Database\Factories;

use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizAnswerFactory extends Factory
{
    protected $model = QuizAnswer::class;

    public function definition(): array
    {
        return [
            'question_id' => QuizQuestion::factory(),
            'answer' => chr(65 + $this->faker->numberBetween(0, 3)), // A, B, C, or D
            'answer_text' => $this->faker->sentence(),
            'is_correct' => false,
            'sequence' => $this->faker->numberBetween(1, 4),
        ];
    }
}
