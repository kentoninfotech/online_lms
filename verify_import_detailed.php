<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Course;

echo "=== DATA STRUCTURE VERIFICATION ===\n\n";

// Check for the imported courses (codes 1-8)
$importedCourses = Course::whereIn('code', ['1', '2', '3', '4', '5', '6', '7', '8'])
    ->with(['courseDates.venues'])
    ->get();

echo "Imported Courses: " . count($importedCourses) . "\n\n";

foreach ($importedCourses as $course) {
    echo "─────────────────────────────────────────────────────────────────\n";
    echo "Course Code: {$course->code}\n";
    echo "Title: {$course->title}\n";
    echo "Total Dates: " . $course->courseDates->count() . "\n";
    
    $totalVenues = $course->courseDates->sum(function($date) {
        return $date->venues->count();
    });
    
    echo "Total Venues: $totalVenues\n";
    
    // Show venues by date
    $dateCount = 0;
    foreach ($course->courseDates as $date) {
        if ($date->venues->count() > 0) {
            $dateCount++;
            echo "\n  [$dateCount] {$date->date_label}\n";
            foreach ($date->venues as $venue) {
                echo "      • {$venue->venue_name}: " . number_format($venue->fee, 2) . " {$course->currency}\n";
            }
        }
    }
    echo "\n";
}

// Verify no duplicate course codes
$duplicateCodes = Course::selectRaw('code, COUNT(*) as count')
    ->whereIn('code', ['1', '2', '3', '4', '5', '6', '7', '8'])
    ->groupBy('code')
    ->having('count', '>', 1)
    ->get();

if ($duplicateCodes->count() > 0) {
    echo "\n⚠️  WARNING: Duplicate courses found:\n";
    foreach ($duplicateCodes as $dup) {
        echo "  Code {$dup->code}: {$dup->count} copies\n";
    }
} else {
    echo "\n✓ No duplicate courses found - each course saved only once\n";
}

echo "\n" . str_repeat("=", 65) . "\n";
echo "✓ Import verification complete!\n";
echo "✓ Data model: 1 Course → Many Dates → Many Venues with individual fees\n";
?>
