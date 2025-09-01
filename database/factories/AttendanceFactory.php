<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LessonOccurrence;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use Illuminate\Support\Carbon;

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
        $join  = Carbon::instance($this->faker->dateTimeBetween('-1 hour', 'now'));
        $leave = (clone $join)->addMinutes($this->faker->numberBetween(15, 60));

        // Randomly choose between Student and Instructor
        // Get a random user and determine attendable type based on user_type
        // $user = User::inRandomOrder()->first() ?? User::factory()->create();
        // if ($user->user_type === 'student') {
        //     $attendableType = \App\Models\Student::class;
        //     $attendableId = Student::where('user_id', $user->id)->first()?->id ?? Student::factory()->create(['user_id' => $user->id])->id;
        // } else {
        //     $attendableType = \App\Models\Instructor::class;
        //     $attendableId = \App\Models\Instructor::where('user_id', $user->id)->first()?->id ?? \App\Models\Instructor::factory()->create(['user_id' => $user->id])->id;
        // }
        
        // $attendableType = $this->faker->randomElement($attendableTypes);

        // Use corresponding factory for attendable_id
        // $attendableId = $attendableType === \App\Models\Student::class
        //     ? \App\Models\Student::factory()
        //     : \App\Models\Instructor::factory();

        return [
            'lesson_occurrence_id' => LessonOccurrence::factory(),
            // 'attendable_type'      => $attendableType,
            // 'attendable_id'        => $attendableId,
            'join_time'            => $join,
            'leave_time'           => $leave,
            'duration_minutes'     => $join->diffInMinutes($leave),
            'status'               => $this->faker->randomElement(['present', 'absent', 'late']),
            'zoom_user_id'         => $this->faker->uuid(),
            'raw'                  => json_encode(['dummy' => true]),
        ];
    }
}
