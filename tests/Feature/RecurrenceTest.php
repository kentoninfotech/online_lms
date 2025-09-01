<?php

use App\Models\Lesson;
use App\Models\LessonOccurrence;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\RecurrenceService;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Artisan;
// use Illuminate\Foundation\Testing\RefreshDatabase;

beforeEach(function () {
    // Freeze time for consistency
    Carbon::setTestNow('2025-01-01 08:00:00');
    parent::setUp();
    $this->artisan('migrate');
});


it('creates a one-off occurrence for non-recurring lesson', function () {
    $student = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'student_id' => $student->id,
        'start_time' => now()->addDay()->setTime(10, 0),
        'duration_minutes' => 60,
        'recurrence_type' => 'none',
    ]);

    expect(LessonOccurrence::count())->toBe(1)
        ->and(LessonOccurrence::first()->scheduled_start)->toEqual($lesson->start_time);
});

it('creates recurring weekly occurrences within plan horizon', function () {
    $plan = Plan::factory()->create([
        'duration_type' => 'weekly',
        'duration_count' => 1, // → 30 day horizon
    ]);

    $student = User::factory()->create();
    Subscription::factory()->create([
        'student_id' => $student->id,
        'plan_id' => $plan->id,
        'start_date' => now(),
        'end_date' => now()->addMonth(),
        'status' => 'active',
    ]);

    $lesson = Lesson::factory()->create([
        'student_id' => $student->id,
        'start_time' => now()->addDay()->setTime(9, 0),
        'duration_minutes' => 45,
        'recurrence_type' => 'weekly',
        'recurrence_meta' => json_encode(['interval' => 1, 'days' => ['Monday', 'Wednesday']]),
    ]);

    $occurrences = LessonOccurrence::all();

    expect($occurrences->count())->toBeGreaterThan(1)
        ->and($occurrences->pluck('scheduled_start')->map(fn($d) => Carbon::parse($d))->min()->isAfter(now()))->toBeTrue()
        ->and($occurrences->pluck('scheduled_start')->map(fn($d) => Carbon::parse($d))->max()->lte(now()->addDays(30)))->toBeTrue();
});

it('regenerates occurrences when lesson updated', function () {
    $student = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'student_id' => $student->id,
        'start_time' => now()->addDay()->setTime(11, 0),
        'duration_minutes' => 30,
        'recurrence_type' => 'none',
    ]);

    expect(LessonOccurrence::count())->toBe(1);

    // Update lesson to recurring weekly
    $lesson->update([
        'recurrence_type' => 'weekly',
        'recurrence_meta' => json_encode(['interval' => 1, 'days' => ['Friday']]),
    ]);

    $occurrences = LessonOccurrence::all();

    expect($occurrences->count())->toBeGreaterThan(1)
        ->and($occurrences->pluck('scheduled_start')->every(fn ($date) => Carbon::parse($date)->isFriday()))->toBeTrue();
});

it('daily artisan command extends horizon without duplicates', function () {
    $plan = Plan::factory()->create([
        'duration_type' => 'weekly',
        'duration_count' => 1,
    ]);
    $student = User::factory()->create();
    Subscription::factory()->create([
        'student_id' => $student->id,
        'plan_id' => $plan->id,
        'start_date' => now(),
        'end_date' => now()->addMonth(),
        'status' => 'active',
    ]);

    $lesson = Lesson::factory()->create([
        'student_id' => $student->id, // <-- associate student
        'recurrence_type' => 'daily',
        'recurrence_meta' => json_encode(['interval' => 1]),
        'start_time' => now()->addDay()->setTime(12, 0),
        'duration_minutes' => 60,
    ]);

    // First run
    Artisan::call('lessons:generate-occurrences --days=10');
    $firstRunCount = LessonOccurrence::count();

    // Run again → should not duplicate
    Artisan::call('lessons:generate-occurrences --days=10');
    $secondRunCount = LessonOccurrence::count();

    expect($firstRunCount)->toBe($secondRunCount);
});

