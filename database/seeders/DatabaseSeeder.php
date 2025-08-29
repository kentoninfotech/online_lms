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

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Settings & Business Rules
        Setting::updateOrCreate(['key' => 'reschedule_limit'], ['value' => 3]);
        Setting::updateOrCreate(['key' => 'reschedule_guard_time_minutes'], ['value' => 120]);
        Setting::updateOrCreate(['key' => 'attendance_grace_period_minutes'], ['value' => 10]);
        Setting::updateOrCreate(['key' => 'billing_grace_period_days'], ['value' => 7]);
        Setting::updateOrCreate(['key' => 'recurrence_horizon_days'], ['value' => 30]);

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
        Lesson::factory(10)->create()->each(function ($lesson) use ($students, $instructors) {
            $occurrence = LessonOccurrence::factory()->create(['lesson_id' => $lesson->id]);
            $zoom = ZoomSession::factory()->create(['lesson_occurrence_id' => $occurrence->id]);

            // random attendance (student + instructor)
            Attendance::factory()->create([
                'lesson_occurrence_id' => $occurrence->id,
                'user_id' => $lesson->student_id,
            ]);
            Attendance::factory()->create([
                'lesson_occurrence_id' => $occurrence->id,
                'user_id' => $lesson->instructor_id,
            ]);

            // Notify Student: Class Reminder
            // $student = User::find($lesson->student_id);
            // $student->notify(new ClassReminder($occurrence));
        });
    }
}

