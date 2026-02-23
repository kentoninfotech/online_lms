<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Executing ALTER TABLE to add 'yes_no' to question_type enum...\n\n";
    
    DB::statement("ALTER TABLE quiz_questions MODIFY question_type ENUM('multiple_choice', 'true_false', 'short_answer', 'essay', 'yes_no') DEFAULT 'multiple_choice'");
    
    echo "✅ Successfully altered quiz_questions table!\n\n";
    
    // Verify the change
    $columns = DB::select("DESCRIBE quiz_questions");
    foreach ($columns as $col) {
        if ($col->Field === 'question_type') {
            echo "Updated Column Definition:\n";
            echo "Field: {$col->Field}\n";
            echo "Type: {$col->Type}\n";
            echo "Default: {$col->Default}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
