<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Course;

// Verify the imported data
echo "=== IMPORT VERIFICATION ===\n\n";

$courses = Course::whereIn('code', ['1', '2', '3'])->with(['courseDates.venues'])->get();

foreach ($courses as $course) {
    echo "Course: {$course->title}\n";
    echo "Code: {$course->code}\n";
    echo "Number of Dates: " . count($course->courseDates) . "\n";
    echo "Currency: {$course->currency}\n\n";
    
    foreach ($course->courseDates as $idx => $date) {
        $dateNum = $idx + 1;
        echo "  Date $dateNum: {$date->date_label}\n";
        echo "  Number of Venues: " . count($date->venues) . "\n";
        
        foreach ($date->venues as $venue) {
            echo "    • {$venue->venue_name}: " . number_format($venue->fee, 2) . " {$course->currency}\n";
        }
        echo "\n";
    }
    
    echo str_repeat("-", 80) . "\n\n";
}

$totalCourses = Course::count();
$totalDates = \App\Models\CourseDate::count();
$totalVenues = \App\Models\CourseVenue::count();

echo "=== SUMMARY ===\n";
echo "Total Courses: $totalCourses\n";
echo "Total Course Dates: $totalDates\n";
echo "Total Course Venues: $totalVenues\n";
echo "\n✓ Import completed successfully!\n";
?>
