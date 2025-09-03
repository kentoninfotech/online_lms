<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;
use App\Models\LessonOccurrence;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\ZoomSession;
use App\Jobs\SyncZoomParticipantsJob;
use App\Models\Setting;

beforeEach(function () {
    parent::setUp();

    $this->artisan('migrate:fresh');
    $this->artisan('db:seed', ['--class' => \Database\Seeders\SettingsSeeder::class]);
    
    Queue::fake();
    Http::preventStrayRequests();
});

/**
 * Test CreateZoomSessions command with Http::fake()
 */
it('creates a ZoomSession when CreateZoomSessions command runs', function () {

    // Setting::updateOrCreate(['key' => 'zoom_meeting_horizon_days'], ['value' => 7]);

    $instructorUser = User::factory()->create(['user_type' => 'instructor']);
    $studentUser    = User::factory()->create(['user_type' => 'student']);

    $instructor = Instructor::factory()->create([
        'zoom_user_id' => 'host123',
        'user_id' => $instructorUser->id,
    ]);
    $student = Student::factory()->create([
        'user_id' => $studentUser->id,
    ]);

    $lesson = Lesson::factory()->create([
        'subject' => 'Math',
        'instructor_id' => $instructor->id,
        'student_id' => $student->id,
        'start_time' => now()->addDay()->setTime(10, 0),
        'duration_minutes' => 60,
    ]);

    $occurrence = LessonOccurrence::factory()->create([
        'lesson_id' => $lesson->id,
        'scheduled_start' => now()->addHours(2), // safely inside horizon
        'duration_minutes' => 60,
        // 'scheduled_start' => now()->addDay()->setTime(10, 0),
    ]);

    // $occurrence = $lesson->occurrences()->first();
    // $occurrence->update([
    //     'scheduled_start' => now()->addHours(2), // safely inside horizon
    //     // 'scheduled_start' => now()->addDay()->setTime(10, 0),
    //     'duration_minutes' => 60,
    // ]);

    Http::fake([
        'https://zoom.us/oauth/token*' => Http::response(['access_token' => 'fake-token'], 200),
        'https://api.zoom.us/v2/users/.*/meetings' => Http::response([
            'id' => '9876543210',
            'join_url' => 'https://zoom.us/j/9876543210',
            'start_url' => 'https://zoom.us/s/9876543210',
        ], 201),
    ]);

    Artisan::call('lessons:create-zoom-sessions');


    $this->assertDatabaseHas('zoom_sessions', [
        'lesson_occurrence_id' => $occurrence->id,
        'zoom_meeting_id'      => '9876543210',
    ]);
});

/**
 * Test SyncZoomParticipantsJob with Http::fake()
 */
it('syncs participants and creates attendance records', function () {
    $instructorUser = User::factory()->create(['user_type' => 'instructor']);
    $studentUser    = User::factory()->create(['user_type' => 'student']);

    $instructor = Instructor::factory()->create([
        'zoom_user_id' => 'host123',
        'user_id' => $instructorUser->id,
    ]);
    $student = Student::factory()->create([
        'zoom_user_id' => 'stu123',
        'user_id' => $studentUser->id,
    ]);

    $lesson = Lesson::factory()->create([
        'student_id' => $student->id,
        'instructor_id' => $instructor->id,
        'start_time' => now()->subDay()->setTime(14, 0),
        'duration_minutes' => 60,
    ]);

    $occurrence = $lesson->occurrences()->first();
    $occurrence->update([
        'scheduled_start' => now()->subDay()->setTime(14, 0),
        'duration_minutes' => 60,
    ]);

    $session = ZoomSession::factory()->create([
        'lesson_occurrence_id' => $occurrence->id,
        'zoom_meeting_id' => '9876543210',
        'status' => 'started',
    ]);

    Http::fake([
        'https://zoom.us/oauth/token' => Http::response(['access_token' => 'fake-token'], 200),
        'https://api.zoom.us/v2/report/meetings/*/participants*' => Http::response([
            'participants' => [
                [
                    'id' => 'stu123',
                    'user_email' => $student->email,
                    'join_time' => now()->subMinutes(45)->toIso8601String(),
                    'leave_time' => now()->subMinutes(5)->toIso8601String(),
                    'duration' => 40,
                ],
            ],
        ], 200),
    ]);

    (new SyncZoomParticipantsJob($session))
        ->handle(app(\App\Services\ZoomService::class));

    $this->assertDatabaseHas('attendances', [
        'lesson_occurrence_id' => $occurrence->id,
        'zoom_user_id' => 'stu123',
        'attendable_type' => Student::class,
        'attendable_id' => $student->id,
        'status' => 'present',
    ]);

    expect($session->fresh()->status)->toBe('ended');
});

/**
 * Test that command does not create duplicate ZoomSessions
 */
it('does not create duplicate ZoomSessions if one already exists', function () {
    $instructorUser = User::factory()->create(['user_type' => 'instructor']);
    $studentUser    = User::factory()->create(['user_type' => 'student']);

    $instructor = Instructor::factory()->create([
        'zoom_user_id' => 'host123',
        'user_id' => $instructorUser->id,
    ]);
    $student = Student::factory()->create([
        'user_id' => $studentUser->id,
    ]);

    $lesson = Lesson::factory()->create([
        'subject' => 'Science',
        'instructor_id' => $instructor->id,
        'student_id' => $student->id,
        'start_time' => now()->addDay()->setTime(11, 0),
        'duration_minutes' => 45,
    ]);

    $occurrence = $lesson->occurrences()->first();
    $occurrence->update([
        'scheduled_start' => now()->addDay()->setTime(11, 0),
        'duration_minutes' => 45,
    ]);

    // Pre-create a ZoomSession for this occurrence
    ZoomSession::factory()->create([
        'lesson_occurrence_id' => $occurrence->id,
        'zoom_meeting_id' => '1111111111',
        'status' => 'scheduled',
    ]);

    Http::fake([
        'https://zoom.us/oauth/token*' => Http::response(['access_token' => 'fake-token'], 200),
        'https://api.zoom.us/v2/users/*/meetings' => Http::response([
            'id' => '2222222222',
            'join_url' => 'https://zoom.us/j/2222222222',
            'start_url' => 'https://zoom.us/s/2222222222',
        ], 201),
    ]);

    Artisan::call('lessons:create-zoom-sessions');

    // Still only one ZoomSession should exist for this occurrence
    $this->assertDatabaseCount('zoom_sessions', 1);

    $this->assertDatabaseHas('zoom_sessions', [
        'lesson_occurrence_id' => $occurrence->id,
        'zoom_meeting_id' => '1111111111',
    ]);
});
