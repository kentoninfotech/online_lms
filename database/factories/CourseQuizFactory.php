<?php

namespace Database\Factories;

use App\Models\CourseQuiz;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseQuizFactory extends Factory
{
    protected $model = CourseQuiz::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'total_questions' => $this->faker->numberBetween(5, 20),
            'passing_score' => $this->faker->numberBetween(50, 80),
            'time_limit_minutes' => $this->faker->numberBetween(30, 120),
            'attempts_allowed' => $this->faker->numberBetween(1, 5),
            'show_correct_answers' => true,
            'shuffle_questions' => $this->faker->boolean(0.5),
            'is_published' => true,
            'sequence' => $this->faker->numberBetween(1, 5),
            'is_required' => $this->faker->boolean(0.6),
        ];
    }
}
