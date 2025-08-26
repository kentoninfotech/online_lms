<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Lesson;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LessonOccurrence>
 */
class LessonOccurrenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'scheduled_start' => $this->faker->dateTimeBetween('now', '+1 week'),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ];
    }
}
