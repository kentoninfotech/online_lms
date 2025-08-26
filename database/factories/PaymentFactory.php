<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Subscription;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'parent_id' => User::factory()->create(['user_type' => 'parent'])->id,
            'amount' => $this->faker->randomFloat(2, 1000, 20000),
            'file_path' => null,
            'status' => 'verified',
        ];
    }
}
