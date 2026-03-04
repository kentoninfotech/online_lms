<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the instructor user
$user = \App\Models\User::find(124);
echo "=== TESTING INSTRUCTOR: {$user->name} ===\n";

try {
    $instructor = $user->instructor;
    echo "Instructor model loaded: YES\n";
    echo "  Instructor ID: {$instructor->id}\n";
    echo "  Instructor Name: {$instructor->name}\n";
    
    // Test the courses relationship
    echo "\n=== TESTING COURSES RELATIONSHIP ===\n";
    $courses = $instructor->courses()->get();
    echo "Courses loaded: YES\n";
    echo "  Total courses: " . $courses->count() . "\n";
    
    // Test activeCourses
    echo "\n=== TESTING ACTIVE COURSES ===\n";
    $activeCourses = $instructor->activeCourses()->get();
    echo "Active courses loaded: YES\n";
    echo "  Total active courses: " . $activeCourses->count() . "\n";
    
    // Test paginated courses
    echo "\n=== TESTING PAGINATED COURSES ===\n";
    $paginatedCourses = $instructor->courses()
        ->with(['category', 'activeInstructors', 'enrollees'])
        ->paginate(12);
    echo "Paginated courses loaded: YES\n";
    echo "  Items per page: " . count($paginatedCourses->items()) . "\n";
    
    // Test stats
    echo "\n=== TESTING STATS CALCULATION ===\n";
    $totalCourses = $instructor->courses()->count();
    $activeCourses = $instructor->activeCourses()->count();
    $totalEnrollees = $instructor->courses()
        ->withCount('enrollees')
        ->get()
        ->sum('enrollees_count');
    
    echo "Statistics calculated: YES\n";
    echo "  Total Courses: $totalCourses\n";
    echo "  Active Courses: $activeCourses\n";
    echo "  Total Enrollees: $totalEnrollees\n";
    
    echo "\n=== ALL TESTS PASSED ===\n";
    echo "The controller method should work fine!\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
