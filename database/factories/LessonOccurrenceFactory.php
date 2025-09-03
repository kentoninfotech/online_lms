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
        static $increment = 0; // increments with each factory call
        
        return [
            'lesson_id'          => Lesson::factory(),
            'scheduled_start'    => now()->addDays(++$increment),
            'duration_minutes'   => 60,
            'status'             => 'scheduled',
        ];
    }
}
