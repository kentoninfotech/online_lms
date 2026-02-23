<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Facilitator;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('COURSE-####'),
            'title' => $this->faker->sentence(4),
            'subtitle' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(3, true),
            'category_id' => CourseCategory::factory(),
            'facilitator_id' => Facilitator::factory(),
            'fee' => $this->faker->numberBetween(10000, 500000),
            'currency' => 'NGN',
            'course_hours' => $this->faker->numberBetween(10, 100),
            'is_online' => $this->faker->boolean(),
            'is_offline' => $this->faker->boolean(),
            'is_featured' => $this->faker->boolean(0.3),
            'is_active' => true,
            'featured_image' => null,
            'max_enrollees' => $this->faker->numberBetween(50, 200),
            'enrolled_count' => 0,
        ];
    }
}
