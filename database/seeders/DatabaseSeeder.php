<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Lesson;
use App\Models\LessonOccurrence;
use App\Models\ZoomSession;
use App\Models\Attendance;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\Instructor;
// use App\Notifications\PaymentApproved;
// use App\Notifications\ClassReminder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Settings & Business Rules
        $this->call(SettingsSeeder::class);

        // Admin
        User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('12345'),
            'user_type' => 'admin',
        ]);

        // Create Parents (User + ParentModel)
        $parentUsers = User::factory(5)->create(['user_type' => 'parent']);
        $parents = $parentUsers->map(function ($user) {
            return ParentModel::factory()->create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile'  => "https://ui-avatars.com/api/?name=" . urlencode($user->name) . "&background=random&size=128",

            ]);
        });

        // Create Students (User + Student)
        $studentUsers = User::factory(10)->create(['user_type' => 'student']);
        $students = $studentUsers->map(function ($user) {
            return Student::factory()->create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile'  => "https://ui-avatars.com/api/?name=" . urlencode($user->name) . "&background=random&size=128",
            ]);
        });
        // Link Students to Parents
        foreach ($students as $student) {
            $student->parents()->attach($parents->random()->id, ['relationship' => 'guardian']);
        }

        // Create Instructors (User + Instructor)
        $instructorUsers = User::factory(5)->create(['user_type' => 'instructor']);
        $instructors = $instructorUsers->map(function ($user) {
            return Instructor::factory()->create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile'  => "https://ui-avatars.com/api/?name=" . urlencode($user->name) . "&background=random&size=128",
            ]);
        });

        // Plans
        $plans = Plan::factory(3)->create();

        // Subscriptions & Payments
        foreach ($students as $student) {
            $sub = Subscription::factory()->create([
                'student_id' => $student->id,
                'plan_id' => $plans->random()->id,
            ]);

            $payment = Payment::factory()->create([
                'subscription_id' => $sub->id,
                'parent_id' => $student->parents()->first()->id,
            ]);

            // Notify Parent: Payment Approved
            // $student->parents()->first()->notify(new PaymentApproved($payment));
        }

        // Lessons + Occurrences + Zoom + Attendance
        $lesson = Lesson::factory(10)->create([
            'student_id'     => $students->random()->id,
            'instructor_id'  => $instructors->random()->id,
        ])->each(function($lesson) use ($students, $instructors) {
            $occurrence = LessonOccurrence::factory()->create([
                'lesson_id'       => $lesson->id,
                'scheduled_start' => now()->addDays(rand(1,7))->setTime(rand(8,17), 0),
            ]);

            // Dev convenience: create a fake ZoomSession immediately
            ZoomSession::factory()->create(['lesson_occurrence_id' => $occurrence->id]);

            // Fake attendance
            $att_status = ['present', 'late'];
            Attendance::factory()->create([
                'lesson_occurrence_id' => $occurrence->id,
                'attendable_type'      => Student::class,
                'attendable_id'        => $lesson->student_id,
                'status'               => $att_status[array_rand($att_status)],
            ]);
            Attendance::factory()->create([
                'lesson_occurrence_id' => $occurrence->id,
                'attendable_type'      => Instructor::class,
                'attendable_id'        => $lesson->instructor_id,
                'status'               => $att_status[array_rand($att_status)],
            ]);

            // Notify Student: Class Reminder
            // $student = User::find($lesson->student_id);
            // $student->notify(new ClassReminder($occurrence));
        });

        /**
         * --- Reschedule Requests + Notifications ---
         */
        $rescheduleService = App::make(RescheduleService::class);

        $sampleOccurrence = LessonOccurrence::inRandomOrder()->first();
        $studentUser = $sampleOccurrence->lesson->student->user;

        // Auto-approved request (within limit)
        $rescheduleService->requestReschedule(
            $sampleOccurrence,
            $studentUser,
            now()->addDays(2)->setTime(10, 0),
            'Need to move class for personal reasons.'
        );

        // Pending request that later gets approved
        $pendingOccurrence = LessonOccurrence::inRandomOrder()->first();
        $pendingReq = $rescheduleService->requestReschedule(
            $pendingOccurrence,
            $pendingOccurrence->lesson->student->user,
            now()->addDays(3)->setTime(14, 0),
            'Scheduling conflict with another activity.'
        );

        // Simulate Admin approval
        $admin = User::where('user_type', 'admin')->first();
        $rescheduleService->approveRequest($pendingReq, auto: false, approver: $admin);

        // Pending request that gets rejected
        $rejectOccurrence = LessonOccurrence::inRandomOrder()->first();
        $rejectReq = $rescheduleService->requestReschedule(
            $rejectOccurrence,
            $rejectOccurrence->lesson->student->user,
            now()->addDays(4)->setTime(16, 0),
            'Traveling, need another time slot.'
        );

        $rescheduleService->rejectRequest($rejectReq, approver: $admin, reason: 'Instructor unavailable at requested time.');
    
        // Seed Reschedule Usage + Requests
        $service = app(RescheduleService::class);

        foreach ($students as $student) {
            $plan = $student->subscription?->plan;
            if (! $plan) continue;

            $limit = $plan->reschedule_limit ?? Setting::where('key','reschedule_limit')->value('value');

            // Create a usage record
            $usage = RescheduleUsage::factory()
                ->state([
                    'student_id' => $student->id,
                    'plan_id'    => $plan->id,
                    'reschedule_count' => rand(0, $limit), // some below, some at limit
                ])
                ->monthly()
                ->create();

            // Pick one occurrence for this student
            $occurrence = LessonOccurrence::whereHas('lesson', fn($q) => $q->where('student_id', $student->id))
                ->inRandomOrder()
                ->first();

            if ($occurrence) {
                // Fire a reschedule request with future date
                $proposedStart = Carbon::now()->addDays(rand(2, 5))->setTime(rand(9, 16), 0);
                $reason = "Student unavailable at original time (seeded)";

                // Let service handle approval / notifications
                $service->requestReschedule($occurrence, $student->user, $proposedStart, $reason);
            }
        }

    }
    
}

