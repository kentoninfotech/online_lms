<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Check the quiz_questions table structure
$columns = Schema::getColumns('quiz_questions');

echo "✅ Quiz Questions Table Structure:\n";
echo "===================================\n\n";

foreach ($columns as $column) {
    echo "Column: {$column['name']}\n";
    echo "  Type: {$column['type']}\n";
    if (isset($column['type_name'])) {
        echo "  Type Name: {$column['type_name']}\n";
    }
    echo "\n";
}

// Try inserting a test record with yes_no type
echo "\n✅ Testing INSERT with yes_no question type:\n";
echo "=============================================\n\n";

try {
    // Get first quiz
    $quiz = DB::table('course_quizzes')->first();
    
    if ($quiz) {
        $result = DB::table('quiz_questions')->insert([
            'quiz_id' => $quiz->id,
            'question' => 'Test Yes/No Question',
            'question_type' => 'yes_no',
            'points' => 1,
            'sequence' => 1,
            'correct_answer' => json_encode('yes'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "✅ Successfully inserted quiz question with question_type = 'yes_no'\n";
        
        // Retrieve and display
        $testRecord = DB::table('quiz_questions')->where('question_type', 'yes_no')->first();
        echo "\nInserted Record:\n";
        echo json_encode($testRecord, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "⚠️ No quizzes found in database. Create a quiz first.\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
