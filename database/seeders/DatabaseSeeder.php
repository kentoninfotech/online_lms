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

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'user_type' => 'admin',
        ]);

        // Parents
        $parents = User::factory(5)->create(['user_type' => 'parent']);

        // Students (linked to parents)
        $students = User::factory(10)->create(['user_type' => 'student']);
        foreach ($students as $student) {
            $student->parents()->attach($parents->random()->id, ['relationship' => 'guardian']);
        }

        // Instructors
        $instructors = User::factory(5)->create(['user_type' => 'instructor']);

        // Plans
        $plans = Plan::factory(3)->create();

        // Subscriptions & Payments
        foreach ($students as $student) {
            $sub = Subscription::factory()->create([
                'student_id' => $student->id,
                'plan_id' => $plans->random()->id,
            ]);

            Payment::factory()->create([
                'subscription_id' => $sub->id,
                'parent_id' => $student->parents()->first()->id,
            ]);
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
        });
    }
}

