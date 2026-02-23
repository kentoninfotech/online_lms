<?php

namespace Database\Factories;

use App\Models\CourseDate;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseDateFactory extends Factory
{
    protected $model = CourseDate::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+6 months');
        $end = (clone $start)->modify('+5 days');

        return [
            'course_id' => Course::factory(),
            'start_date' => $start,
            'end_date' => $end,
            'date_label' => $start->format('d - ') . $end->format('d M, Y'),
            'sequence' => $this->faker->numberBetween(1, 4),
            'notes' => $this->faker->sentence(),
        ];
    }
}
