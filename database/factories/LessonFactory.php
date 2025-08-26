<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => $this->faker->randomElement(['Math', 'English', 'Science', 'History']),
            'instructor_id' => User::where('user_type', 'instructor')->inRandomOrder()->first()->id ?? User::factory()->create(['user_type'=>'instructor'])->id,
            'student_id' => User::where('user_type', 'student')->inRandomOrder()->first()->id ?? User::factory()->create(['user_type'=>'student'])->id,
            'start_time' => $this->faker->dateTimeBetween('now', '+1 week'),
            'duration_minutes' => 60,
            'recurrence_type' => $this->faker->randomElement(['none','daily','weekly']),
            'recurrence_meta' => null,
        ];
    }
}
