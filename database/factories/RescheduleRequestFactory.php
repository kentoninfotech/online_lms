<?php

namespace Database\Factories;

use App\Models\RescheduleRequest;
use App\Models\LessonOccurrence;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RescheduleRequestFactory extends Factory
{
    protected $model = RescheduleRequest::class;

    public function definition()
    {
        return [
            'lesson_occurrence_id' => LessonOccurrence::factory(),
            'requested_by'         => User::factory(),
            'proposed_start'       => Carbon::now()->addDays($this->faker->numberBetween(1, 5)),
            'reason'               => $this->faker->sentence(),
            'status'               => 'pending',
            'approved_by'          => null,
            'decision_reason'      => null,
        ];
    }

    public function approved()
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'approved_by' => User::factory(),
        ]);
    }

    public function rejected()
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'approved_by' => User::factory(),
            'decision_reason' => $this->faker->sentence(),
        ]);
    }
}
