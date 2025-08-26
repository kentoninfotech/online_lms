<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Daily Plan','Weekly Plan','Monthly Plan']),
            'price' => $this->faker->randomFloat(2, 1000, 20000),
            'duration_days' => $this->faker->randomElement([1,7,30]),
            'features' => json_encode(['Zoom Access','Reports','Attendance']),
            // 'features' => $this->faker->randomElement(['Zoom Access','Reports','Attendance']),
        ];
    }
}
