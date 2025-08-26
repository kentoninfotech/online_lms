<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Plan;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::factory()->create(['user_type' => 'student'])->id,
            'plan_id' => Plan::factory(),
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
        ];
    }
}
