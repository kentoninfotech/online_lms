<?php

namespace Database\Factories;

use App\Models\Facilitator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacilitatorFactory extends Factory
{
    protected $model = Facilitator::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'bio' => $this->faker->paragraph(),
            'profile_image' => null,
            'qualification' => $this->faker->word(),
            'expertise' => $this->faker->sentence(),
            'is_verified' => true,
            'is_active' => true,
        ];
    }
}
