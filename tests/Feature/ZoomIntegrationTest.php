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
use App\Models\Attendance;
use App\Jobs\SyncZoomParticipantsJob;
use Illuminate\Support\Carbon;

beforeEach(function () {
    parent::setUp();
    $this->artisan('migrate');
    Queue::fake();
    Http::preventStrayRequests();
});

/**
 * Test CreateZoomSessions command with Http::fake()
 */
it('creates a ZoomSession when CreateZoomSessions command runs', function () {
    // Setup fake instructor with zoom_user_id
    $instructor = Instructor::factory()->create([
        'zoom_user_id' => 'host123', 
        'user_id' => User::factory()->create(['user_type' => 'instructor'])->id
    ]);
    $student = Student::factory()->create(['user_id' => User::factory()->create(['user_type' => 'student'])->id]);

    $lesson = Lesson::factory()->create([
        'subject' => 'Math',
        'instructor_id' => $instructor->id,
        'student_id' => $student->id,
        'start_time' => now()->addDay()->setTime(10, 0),
        'duration_minutes' => 60,
    ]);

    $occurrence = LessonOccurrence::factory()->create([
        'lesson_id' => $lesson->id,
        // 'scheduled_start' => now()->addDay()->setTime(10, 0),
        // 'duration_minutes' => 60,
    ]);

    // $occurrence = LessonOccurrence::where('lesson_id', $lesson->id)->first();

    // Fake Zoom OAuth + meeting creation response
    Http::fake([
        'https://zoom.us/oauth/token*' => Http::response([
            'access_token' => 'fake-token',
            // 'expires_in' => 3600,
        ], 200),
        'https://api.zoom.us/v2/users/*/meetings' => Http::response([
            'id' => '9876543210',
            'join_url' => 'https://zoom.us/j/9876543210',
            'start_url' => 'https://zoom.us/s/9876543210',
            // 'password' => 'abc123',
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
    $usr_instructor = User::factory()->create(['user_type' => 'instructor']);
    $usr_student = User::factory()->create(['user_type' => 'student']);
    $instructor = Instructor::factory()->create(['zoom_user_id' => 'host123', 'user_id' => $usr_instructor->id]);
    $student = Student::factory()->create(['zoom_user_id' => 'stu123', 'user_id' => $usr_student->id]);
    $lesson = Lesson::factory()->create(['student_id' => $student->id, 'instructor_id' => $instructor->id]);
    $occurrence = LessonOccurrence::factory()->create(['lesson_id' => $lesson->id]);

    $session = ZoomSession::factory()->create([
        'lesson_occurrence_id' => $occurrence->id,
        'zoom_meeting_id' => '9876543210',
        'status' => 'started',
    ]);

    // Fake Zoom token + participants response
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

    // Dispatch job synchronously
    (new SyncZoomParticipantsJob($session))->handle(app(\App\Services\ZoomService::class));

    $this->assertDatabaseHas('attendances', [
        'lesson_occurrence_id' => $occurrence->id,
        'zoom_user_id' => 'stu123',
        'attendable_type' => Student::class,
        'attendable_id' => $student->id,
        'status' => 'present',
    ]);

    expect($session->fresh()->status)->toBe('ended');
});
