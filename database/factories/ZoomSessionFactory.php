<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LessonOccurrence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ZoomSession>
 */
class ZoomSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lesson_occurrence_id' => LessonOccurrence::factory(),
            'zoom_meeting_id' => $this->faker->uuid,
            'topic' => $this->faker->sentence(3),
            'join_url' => $this->faker->url,
            'start_url' => $this->faker->url,
        ];
    }
}
