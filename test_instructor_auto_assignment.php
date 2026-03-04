<?php
/**
 * Test script to verify instructor auto-assignment when course facilitators are selected
 */

require_once __DIR__ . '/bootstrap/app.php';
require_once __DIR__ . '/vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "Testing Instructor Auto-Assignment\n";
    echo "===================================\n\n";

    // Get a course with facilitators
    $course = \App\Models\Course::with('facilitators', 'instructors')->first();
    
    if (!$course) {
        echo "❌ No courses found in database.\n";
        exit(1);
    }

    echo "Course: {$course->title}\n";
    echo "ID: {$course->id}\n\n";

    // Get their facilitators
    $facilitators = $course->facilitators()->get();
    echo "Facilitators assigned: " . count($facilitators) . "\n";
    
    if (count($facilitators) > 0) {
        foreach ($facilitators as $facilitator) {
            echo "  - {$facilitator->name} (ID: {$facilitator->id}, User: {$facilitator->user_id})\n";
        }
    }
    echo "\n";

    // Get their instructors
    $instructors = $course->instructors()->get();
    echo "Instructors assigned: " . count($instructors) . "\n";
    
    if (count($instructors) > 0) {
        foreach ($instructors as $instructor) {
            echo "  - {$instructor->name} (ID: {$instructor->id}, User: {$instructor->user_id})\n";
            echo "    Role: {$instructor->pivot->role}\n";
            echo "    Can Manage Content: " . ($instructor->pivot->can_manage_content ? 'YES' : 'NO') . "\n";
            echo "    Can Manage Quizzes: " . ($instructor->pivot->can_manage_quizzes ? 'YES' : 'NO') . "\n";
            echo "    Can Manage Enrollees: " . ($instructor->pivot->can_manage_enrollees ? 'YES' : 'NO') . "\n";
            echo "    Is Active: " . ($instructor->pivot->is_active ? 'YES' : 'NO') . "\n\n";
        }
    }

    // Test assignment - simulate what the controller does
    echo "Testing Instructor Assignment Logic\n";
    echo "===================================\n\n";

    // Get facilitator IDs
    $facilitatorIds = $facilitators->pluck('id')->toArray();
    
    if (count($facilitatorIds) == 0) {
        echo "⚠️  No facilitators to test with.\n";
    } else {
        echo "Facilitator IDs to assign: " . implode(', ', $facilitatorIds) . "\n\n";

        // Simulate the controller method
        foreach ($facilitators as $facilitator) {
            if (!$facilitator->user) {
                echo "⚠️  Facilitator {$facilitator->id} has no user\n";
                continue;
            }

            echo "Processing facilitator: {$facilitator->name}\n";

            // Find or create instructor
            $instructor = \App\Models\Instructor::firstOrCreate(
                ['user_id' => $facilitator->user_id],
                [
                    'name' => $facilitator->user->name ?? $facilitator->name,
                    'email' => $facilitator->user->email ?? $facilitator->email,
                    'bio' => $facilitator->bio,
                ]
            );

            echo "  → Instructor (ID: {$instructor->id}, User: {$instructor->user_id}) created/found\n";

            // Check if already attached
            $exists = $course->instructors()->where('instructor_id', $instructor->id)->exists();
            if ($exists) {
                echo "  → Already attached to course\n";
            } else {
                echo "  → Not attached yet\n";
            }
        }
    }

    echo "\n✅ Test completed successfully!\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
