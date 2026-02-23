<?php

namespace Database\Factories;

use App\Models\CourseContent;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseContentFactory extends Factory
{
    protected $model = CourseContent::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'content_type' => $this->faker->randomElement(['text', 'pdf', 'video', 'image']),
            'content' => $this->faker->paragraphs(5, true),
            'file_path' => null,
            'duration_minutes' => $this->faker->numberBetween(15, 120),
            'sequence' => $this->faker->numberBetween(1, 10),
            'section_id' => null,
            'is_published' => true,
            'is_required' => $this->faker->boolean(0.8),
        ];
    }
}
