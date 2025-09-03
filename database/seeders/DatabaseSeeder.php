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

    }
}

