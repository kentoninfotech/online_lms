<?php
// Test CSV Import Functionality

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseCategory;
use App\Services\CourseCSVImportService;

try {
    // Get or create a test category
    $category = CourseCategory::firstOrCreate(
        ['name' => 'Test Category'],
        [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'For testing',
            'is_active' => true
        ]
    );
    
    // Copy the courses.csv to the temp directory to simulate the upload
    $sourcePath = __DIR__ . '/courses.csv';
    $tempDir = storage_path('app/temp');
    
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
    }
    
    $destPath = $tempDir . '/test_courses.csv';
    if (!copy($sourcePath, $destPath)) {
        throw new Exception("Failed to copy file to temp directory");
    }
    
    echo "✓ File copied to temp directory\n";
    echo "  Source: $sourcePath\n";
    echo "  Dest: $destPath\n";
    echo "  File exists: " . (file_exists($destPath) ? "YES" : "NO") . "\n\n";
    
    // Test the import service
    $importService = new CourseCSVImportService();
    
    $result = $importService->import($destPath, $category->id, 'csv');
    
    echo "═══════════════════════════════════════════════════\n";
    echo "IMPORT RESULT:\n";
    echo "═══════════════════════════════════════════════════\n";
    echo "Success: " . ($result['success'] ? "YES" : "NO") . "\n";
    echo "Imported: " . $result['imported'] . " courses\n";
    echo "Errors: " . $result['errors_count'] . "\n";
    
    if ($result['errors_count'] > 0) {
        echo "\nError Details:\n";
        foreach ($result['errors'] as $error) {
            echo "  - $error\n";
        }
    }
    
    echo "\n✓ CSV Import Test Completed Successfully!\n";
    
    // Cleanup
    @unlink($destPath);
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
    exit(1);
}
?>
