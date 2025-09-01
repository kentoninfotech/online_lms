<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'       => null,
            'name'          => $this->faker->name,
            'email'         => $this->faker->unique()->safeEmail(),
            'number'        => $this->faker->phoneNumber,
            'address'       => $this->faker->address,
            'zoom_user_id'  => $this->faker->uuid,
        ];
    }
}
