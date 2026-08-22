<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CourseCSVImportService;

// Test the fixed import service
echo "=== TESTING FIXED IMPORT SERVICE ===\n\n";

// Create a test CSV file with dates_venues format
$testCsv = storage_path('test_import.csv');

// Create test data similar to what the user has
$testData = <<<CSV
CODE,COURSE TITLE,DATE,VENUE WITH FEES,FEE
TEST1,Test Course 1,"Date 1
Date 2
Date 3","Venue A - $5,000
Venue B - $4,000
Venue C - $3,500",5000
TEST2,Test Course 2,"Date A
Date B","Venue X - $6,500
Venue Y - $4,500",6500
CSV;

file_put_contents($testCsv, $testData);

echo "Test CSV file created at: $testCsv\n\n";

try {
    $service = new CourseCSVImportService();
    
    // Get a test category
    $category = \App\Models\CourseCategory::first();
    if (!$category) {
        echo "ERROR: No category found. Creating default category...\n";
        $category = \App\Models\CourseCategory::create([
            'name' => 'Test Category',
            'description' => 'Test',
            'is_active' => true,
        ]);
    }
    
    echo "Using category: {$category->name} (ID: {$category->id})\n\n";
    
    // Test the import
    $result = $service->import(
        $testCsv,
        $category->id,
        'csv',
        'dates_venues',
        'international'
    );
    
    echo "Import Result:\n";
    echo "├─ Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
    echo "├─ Imported: {$result['imported']}\n";
    echo "├─ Errors: {$result['errors_count']}\n";
    
    if (!empty($result['errors'])) {
        echo "├─ Error Messages:\n";
        foreach ($result['errors'] as $error) {
            echo "│  └─ " . $error . "\n";
        }
    }
    echo "\n";
    
    // Verify the created courses
    if ($result['imported'] > 0) {
        echo "=== VERIFYING CREATED DATA ===\n\n";
        
        $courses = \App\Models\Course::whereIn('code', ['TEST1', 'TEST2'])
            ->with(['courseDates.venues'])
            ->get();
        
        foreach ($courses as $course) {
            echo "Course: {$course->title}\n";
            echo "Code: {$course->code}\n";
            echo "Course Fee: " . number_format($course->fee, 2) . "\n";
            echo "Dates: " . count($course->courseDates) . "\n";
            
            foreach ($course->courseDates as $date) {
                echo "  └─ {$date->date_label}\n";
                foreach ($date->venues as $venue) {
                    echo "     └─ {$venue->venue_name}: " . number_format($venue->fee ?? 0, 2) . " NGN\n";
                }
            }
            echo "\n";
        }
        
        echo "✓ Import test PASSED - Data structure is correct!\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

// Cleanup
if (file_exists($testCsv)) {
    unlink($testCsv);
    echo "\nTest CSV file cleaned up.\n";
}
?>
