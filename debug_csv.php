<?php
// Debug CSV Import - Check column mapping

require __DIR__ . '/vendor/autoload.php';

$filePath = __DIR__ . '/courses.csv';

// Read the CSV file
$rows = [];
if (($handle = fopen($filePath, 'r')) !== false) {
    while (($row = fgetcsv($handle)) !== false) {
        $rows[] = array_map('trim', $row ?? []);
    }
    fclose($handle);
}

if (empty($rows)) {
    echo "No data found in CSV\n";
    exit(1);
}

$header = array_shift($rows);

echo "HEADER ROW:\n";
foreach ($header as $i => $col) {
    echo "  [$i] '$col' => normalized: '" . strtolower(trim($col)) . "'\n";
}

// Test the column mapping logic
$validColumns = [
    'code', 'title', 'subtitle', 'description', 'facilitator_id',
    'fee', 'currency', 'course_hours', 'is_online', 'is_offline',
    'is_featured', 'is_active', 'max_enrollees', 'date', 'venue'
];

$map = [];
foreach ($header as $index => $column) {
    $normalizedColumn = strtolower(trim($column));
    
    // Remove extra spaces and underscores for matching
    $cleanedColumn = str_replace([' ', '_', '-'], '', $normalizedColumn);
    
    echo "\nProcessing column [$index]: '$column'\n";
    echo "  Normalized: '$normalizedColumn'\n";
    echo "  Cleaned: '$cleanedColumn'\n";
    
    // Try exact match first
    if (in_array($normalizedColumn, $validColumns)) {
        $map[$normalizedColumn] = $index;
        echo "  → Exact match found: '$normalizedColumn'\n";
        continue;
    }
    
    // Try fuzzy match
    foreach ($validColumns as $valid) {
        $cleanedValid = str_replace([' ', '_', '-'], '', $valid);
        
        echo "  Checking against '$valid' (cleaned:'$cleanedValid')\n";
        echo "    - cleanedColumn=cleanedValid? " . ($cleanedColumn === $cleanedValid ? "YES" : "NO") . "\n";
        echo "    - normalizedColumn contains valid? " . (strpos($normalizedColumn, $valid) !== false ? "YES" : "NO") . "\n";
        echo "    - cleanedColumn contains cleanedValid? " . (strpos($cleanedColumn, $cleanedValid) !== false ? "YES" : "NO") . "\n";
        
        if ($cleanedColumn === $cleanedValid ||
            strpos($normalizedColumn, $valid) !== false ||
            strpos($cleanedColumn, $cleanedValid) !== false) {
            $map[$valid] = $index;
            echo "  → Fuzzy match found: '$valid'\n";
            break;
        }
    }
}

echo "\n\nFINAL COLUMN MAP:\n";
print_r($map);

echo "\n\nFIRST DATA ROW:\n";
print_r($rows[0]);

echo "\n\nVALUES FROM FIRST ROW:\n";
if (isset($map['code'])) {
    echo "Code column [" . $map['code'] . "]: '" . $rows[0][$map['code']] . "'\n";
} else {
    echo "Code column: NOT MAPPED\n";
}

if (isset($map['title'])) {
    echo "Title column [" . $map['title'] . "]: '" . $rows[0][$map['title']] . "'\n";
} else {
    echo "Title column: NOT MAPPED\n";
}
?>
