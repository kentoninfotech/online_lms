<?php

namespace Database\Factories;

use App\Models\CourseEnrollee;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseDate;
use App\Models\CourseVenue;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseEnrolleeFactory extends Factory
{
    protected $model = CourseEnrollee::class;

    public function definition(): array
    {
        $date = CourseDate::factory()->create();

        return [
            'user_id' => User::factory(),
            'course_id' => $date->course_id,
            'course_date_id' => $date->id,
            'course_venue_id' => CourseVenue::factory()->for($date)->create()->id,
            'status' => 'active',
            'payment_status' => 'completed',
            'amount_paid' => $this->faker->numberBetween(10000, 100000),
            'transaction_id' => $this->faker->unique()->bothify('TXN-####-####'),
            'payment_date' => now(),
            'enrolled_at' => now(),
            'completed_at' => null,
            'progress_percentage' => 0,
            'notes' => $this->faker->sentence(),
        ];
    }
}
