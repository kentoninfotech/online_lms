<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LessonOccurrence;
use App\Models\ZoomSession;

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
    protected $model = ZoomSession::class;

    public function definition(): array
    {
        return [
            'lesson_occurrence_id' => LessonOccurrence::factory(),
            'zoom_meeting_id' => (string) $this->faker->numberBetween(1000000000, 9999999999),
            'join_url' => $this->faker->url(),
            'start_url' => $this->faker->url(),
            'status' => $this->faker->randomElement(['scheduled','started','ended']),
            'raw' => ['mock' => true],
        ];
    }
}
