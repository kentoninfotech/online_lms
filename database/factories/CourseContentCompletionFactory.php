<?php

namespace Database\Factories;

use App\Models\CourseContentCompletion;
use App\Models\CourseEnrollee;
use App\Models\CourseContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseContentCompletionFactory extends Factory
{
    protected $model = CourseContentCompletion::class;

    public function definition(): array
    {
        return [
            'course_enrollee_id' => CourseEnrollee::factory(),
            'course_content_id' => CourseContent::factory(),
            'time_spent_minutes' => $this->faker->numberBetween(5, 120),
            'is_completed' => $this->faker->boolean(0.7),
            'completed_at' => $this->faker->boolean(0.7) ? now() : null,
            'started_at' => now()->subHours($this->faker->numberBetween(1, 48)),
            'progress_percentage' => $this->faker->numberBetween(0, 100),
        ];
    }
}
