<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;
use App\Models\LessonOccurrence;
use App\Models\ZoomSession;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\User;

beforeEach(function () {
    parent::setUp();
    $this->artisan('migrate');
    Http::preventStrayRequests();
});

/**
 * Simulate Zoom webhook: meeting.started
 */
it('marks ZoomSession as started when webhook meeting.started is received', function () {
    $instructor = Instructor::factory()->create(['user_id' => User::factory()->create(['user_type' => 'instructor'])->id]);
    $student = Student::factory()->create(['user_id' => User::factory()->create(['user_type' => 'student'])->id]);
    $lesson = Lesson::factory()->create([
        'student_id' => $student->id,
        'instructor_id' => $instructor->id,
    ]);
    $occurrence = LessonOccurrence::factory()->create(['lesson_id' => $lesson->id]);

    $session = ZoomSession::factory()->create([
        'lesson_occurrence_id' => $occurrence->id,
        'zoom_meeting_id' => '9876543210',
        'status' => 'scheduled',
    ]);

    $payload = [
        "event" => "meeting.started",
        "payload" => [
            "object" => [
                "id" => "9876543210",
            ],
        ],
    ];

    $response = $this->postJson('/webhooks/zoom', $payload);
    $response->assertStatus(200);

    expect($session->fresh()->status)->toBe('started');
});

/**
 * Simulate Zoom webhook: meeting.ended
 */
it('marks ZoomSession as ended when webhook meeting.ended is received', function () {
    $instructor = Instructor::factory()->create(['user_id' => User::factory()->create(['user_type' => 'instructor'])->id]);
    $student = Student::factory()->create(['user_id' => User::factory()->create(['user_type' => 'student'])->id]);
    $lesson = Lesson::factory()->create([
        'student_id' => $student->id,
        'instructor_id' => $instructor->id,
    ]);
    $occurrence = LessonOccurrence::factory()->create(['lesson_id' => $lesson->id]);

    $session = ZoomSession::factory()->create([
        'lesson_occurrence_id' => $occurrence->id,
        'zoom_meeting_id' => '1234567890',
        'status' => 'started',
    ]);

    $payload = [
        "event" => "meeting.ended",
        "payload" => [
            "object" => [
                "id" => "1234567890",
            ],
        ],
    ];

    $response = $this->postJson('/webhooks/zoom', $payload);
    $response->assertStatus(200);

    expect($session->fresh()->status)->toBe('ended');
});

/**
 * Handles unknown meeting ID gracefully
 */
it('ignores webhook if meeting ID not found', function () {
    $payload = [
        "event" => "meeting.ended",
        "payload" => [
            "object" => [
                "id" => "not-existing",
            ],
        ],
    ];

    $response = $this->postJson('/webhooks/zoom', $payload);
    $response->assertStatus(200); // still OK
});
