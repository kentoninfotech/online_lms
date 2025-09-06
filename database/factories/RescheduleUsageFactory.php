<?php

namespace Database\Factories;

use App\Models\RescheduleUsage;
use App\Models\Student;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RescheduleUsageFactory extends Factory
{
    protected $model = RescheduleUsage::class;

    public function definition()
    {
        $now = now();
        return [
            'student_id'      => Student::factory(),
            'plan_id'         => Plan::factory(),
            'period_start'    => $now->copy()->startOfMonth(),
            'period_end'      => $now->copy()->endOfMonth(),
            'reschedule_count'=> $this->faker->numberBetween(0, 5),
        ];
    }

    public function daily()
    {
        $now = now();
        return $this->state(fn() => [
            'period_start' => $now->copy()->startOfDay(),
            'period_end'   => $now->copy()->endOfDay(),
        ]);
    }

    public function weekly()
    {
        $now = now();
        return $this->state(fn() => [
            'period_start' => $now->copy()->startOfWeek(),
            'period_end'   => $now->copy()->endOfWeek(),
        ]);
    }

    public function monthly()
    {
        $now = now();
        return $this->state(fn() => [
            'period_start' => $now->copy()->startOfMonth(),
            'period_end'   => $now->copy()->endOfMonth(),
        ]);
    }
}
