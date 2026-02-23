<?php

namespace Database\Factories;

use App\Models\CourseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseCategoryFactory extends Factory
{
    protected $model = CourseCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraph(),
            'color' => $this->faker->hexColor(),
            'icon' => 'book',
            'is_active' => true,
            'sort_order' => $this->faker->randomNumber(2),
        ];
    }
}
