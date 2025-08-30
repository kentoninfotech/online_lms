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
        $plans = [
            [
                'name' => 'Daily Plan',
                'duration_type' => 'daily',
                'price' => 1000,
                'duration_count' => 1,
            ],
            [
                'name' => 'Weekly Plan',
                'duration_type' => 'weekly',
                'price' => 5000,
                'duration_count' => 7,
            ],
            [
                'name' => 'Monthly Plan',
                'duration_type' => 'monthly',
                'price' => 15000,
                'duration_count' => 30,
            ],
        ];

        $plan = $this->faker->randomElement($plans);

        return [
            'name' => $plan['name'],
            'price' => $plan['price'],
            'duration_type' => $plan['duration_type'],
            'duration_count' => $plan['duration_count'],
            'reschedule_limit' => $this->faker->numberBetween(0,5),
            'payment_grace_days' => $this->faker->randomElement([null,3,5,7]),
            'features' => json_encode(['Zoom Access','Reports','Attendance']),
        ];
    }
}
