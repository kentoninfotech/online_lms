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
use App\Models\RescheduleUsage;
use App\Services\RescheduleService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Carbon;
use App\Notifications\Admin\PaymentSubmitted;
use App\Notifications\Parent\PaymentApproved;
use App\Notifications\Parent\PaymentRejected;
use Illuminate\Support\Str;
use Illuminate\Notifications\DatabaseNotification;
// use App\Notifications\PaymentApproved;
// use App\Notifications\ClassReminder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Settings & Business Rules
        $this->call(SettingsSeeder::class);
        // Roles and Permissions
        $this->call(RoleSeeder::class);

        // Admin
        $admin = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('12345'),
            'user_type' => 'admin',
        ]);
        // Assign Admin role
        $admin->assignRole('admin');

        // Create Parents (User + ParentModel)
        $parentUsers = User::factory(5)->create(['user_type' => 'parent']);
        $parents = $parentUsers->map(function ($user) {
            // Assign parent role
            $user->assignRole('parent');

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
            // Assign student role
            $user->assignRole('student');

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
            // Assign instructor role
            $user->assignRole('instructor');
            
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

            $parent = $student->parents()->first();
            $admin  = User::where('user_type', 'admin')->first();

            // random status for this payment
            $status = collect(['approved', 'pending', 'rejected'])->random();

            $payment = Payment::factory()->create([
                'subscription_id' => $sub->id,
                'parent_id'       => $parent->id,
                'status'          => $status,
                'decision_reason' => $status === 'rejected' ? 'Invalid proof of payment' : null,
            ]);

            // Fire notifications + DB logs
            if ($status === 'approved') {
                $parent->user->notify(new PaymentApproved($payment));

                DatabaseNotification::create([
                    'id'              => (string) Str::uuid(),
                    'type'            => PaymentApproved::class,
                    'notifiable_type' => User::class,
                    'notifiable_id'   => $parent->user->id,
                    'data' => [
                        'message'    => "Your payment for subscription #{$sub->id} has been approved.",
                        'payment_id' => $payment->id,
                    ],
                ]);
            }

            if ($status === 'pending' && $admin) {
                $admin->notify(new PaymentSubmitted($payment));

                DatabaseNotification::create([
                    'id'              => (string) Str::uuid(),
                    'type'            => PaymentSubmitted::class,
                    'notifiable_type' => User::class,
                    'notifiable_id'   => $admin->id,
                    'data' => [
                        'message'    => "A new payment was submitted for subscription #{$sub->id}.",
                        'payment_id' => $payment->id,
                    ],
                ]);
            }

            if ($status === 'rejected') {
                $parent->user->notify(new PaymentRejected($payment, $payment->decision_reason));

                DatabaseNotification::create([
                    'id'              => (string) Str::uuid(),
                    'type'            => PaymentRejected::class,
                    'notifiable_type' => User::class,
                    'notifiable_id'   => $parent->user->id,
                    'data' => [
                        'message'    => "Your payment for subscription #{$sub->id} was rejected. Reason: {$payment->decision_reason}",
                        'payment_id' => $payment->id,
                    ],
                ]);
            }

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
                'scheduled_start' => now()->addDays(rand(2,7))->setTime(rand(8,17), 0),
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

        // Use 3 different students with subscriptions/lessons for variety
        $studentsForReschedule = Student::has('lessons.occurrences')
            ->take(3)
            ->get();

        if ($studentsForReschedule->count() >= 3) {
            // Auto-approved request (within limit)
            $sampleOccurrence = $studentsForReschedule[0]->lessons()->first()->occurrences()->first();
            $rescheduleService->requestReschedule(
                $sampleOccurrence,
                $studentsForReschedule[0]->user,
                now()->addDays(3)->setTime(11, 0), // ensure guard time > 2 days
                'Need to move class for personal reasons.'
            );

            // Pending request that later gets approved
            $pendingOccurrence = $studentsForReschedule[1]->lessons()->first()->occurrences()->first();
            $pendingReq = $rescheduleService->requestReschedule(
                $pendingOccurrence,
                $studentsForReschedule[1]->user,
                now()->addDays(4)->setTime(14, 0), // always future safe
                'Scheduling conflict with another activity.'
            );
            $admin = User::where('user_type', 'admin')->first();
            $rescheduleService->approveRequest($pendingReq, auto: false, approver: $admin);

            // Pending request that gets rejected
            $rejectOccurrence = $studentsForReschedule[2]->lessons()->first()->occurrences()->first();
            $rejectReq = $rescheduleService->requestReschedule(
                $rejectOccurrence,
                $studentsForReschedule[2]->user,
                now()->addDays(5)->setTime(16, 0), // always safe
                'Traveling, need another time slot.'
            );
            $rescheduleService->rejectRequest($rejectReq, approver: $admin, reason: 'Instructor unavailable at requested time.');
        }

        // Seed Reschedule Usage + Requests
        $service = app(RescheduleService::class);

        foreach ($students as $student) {
            // Ensure student has a subscription + plan
            $subscription = $student->subscriptions;
            if (! $subscription || ! $subscription->plan) {
                continue; // skip students without plan
            }
            // Get plan
            $plan = $student->subscription?->plan;
            // Get reschedule limit from plan or global setting
            $limit = $plan->reschedule_limit ?? Setting::where('key','reschedule_limit')->value('value');

            // Create a usage record (firstOrCreate to avoid duplicates)
            $usage = RescheduleUsage::firstOrCreate([
                'student_id'  => $student->id,
                'plan_id'     => $plan->id,
                'period_start'=> now()->startOfMonth(),
                'period_end'  => now()->endOfMonth(),
            ], [
                'reschedule_count' => rand(0, $limit),
            ]);

            // Pick one occurrence for this student
            $occurrence = LessonOccurrence::whereHas('lesson', fn($q) => $q->where('student_id', $student->id))
                ->inRandomOrder()
                ->first();

            if (! $occurrence) {
                // create one if missing
                $lesson = Lesson::factory()->create([
                    'student_id'    => $student->id,
                    'instructor_id' => $instructors->random()->id,
                ]);

                $occurrence = LessonOccurrence::factory()->create([
                    'lesson_id'       => $lesson->id,
                    'scheduled_start' => now()->addDays(3)->setTime(rand(9, 16), 0),
                ]);
            } else {
                // Create a reschedule request for this occurrence
                $proposedStart = now()->addDays(rand(6, 10))->setTime(rand(9, 16), 0);
                $reason = "Student unavailable at original time (seeded)";
                // Create the request via service to ensure all logic/notifications are handled
                $service->requestReschedule($occurrence, $student->user, $proposedStart, $reason);
            }
        }

        // Fake reschedule request that violates guard time (auto rejected by system)
        try {
            $tooSoonOccurrence = LessonOccurrence::inRandomOrder()->first();
            $tooSoonReq = $rescheduleService->requestReschedule(
                $tooSoonOccurrence,
                $tooSoonOccurrence->lesson->student->user,
                now()->addMinutes(30), // within guard time, will fail
                'Emergency reschedule attempt within guard window.'
            );
        } catch (\Exception $e) {
            // Store a rejected request manually to simulate system auto-rejection
            \App\Models\RescheduleRequest::create([
                'lesson_occurrence_id' => $tooSoonOccurrence->id,
                'requested_by'         => $tooSoonOccurrence->lesson->student->user->id,
                'proposed_start'       => now()->addMinutes(30),
                'reason'               => 'Emergency reschedule attempt within guard window.',
                'status'               => 'rejected',
                'decision_reason'      => $e->getMessage(), // "Cannot reschedule within X minutes"
            ]);
        }


    }
    
}

