<?php

namespace Database\Factories;

use App\Models\CourseVenue;
use App\Models\CourseDate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseVenueFactory extends Factory
{
    protected $model = CourseVenue::class;

    protected static $locations = [
        'Lagos',
        'Abuja',
        'Nasarawa',
        'Port Harcourt',
        'Ibadan',
        'Kano',
        'Bauchi',
        'Enugu',
    ];

    public function definition(): array
    {
        $location = $this->faker->randomElement(self::$locations);

        return [
            'course_date_id' => CourseDate::factory(),
            'venue_name' => $location,
            'address' => $this->faker->streetAddress(),
            'city' => $location,
            'state' => $location,
            'country' => 'Nigeria',
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'capacity' => $this->faker->numberBetween(30, 100),
            'enrolled_count' => 0,
            'notes' => $this->faker->sentence(),
        ];
    }
}
