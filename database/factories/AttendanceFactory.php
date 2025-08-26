<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LessonOccurrence;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
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
            'user_id' => User::factory(),
            'join_time' => now(),
            'leave_time' => now()->addHour(),
            'duration_minutes' => 60,
            'status' => 'present',
            'zoom_user_id' => $this->faker->uuid,
        ];
    }
}
