<?php

// Read the first row from CSV to see the actual structure
$csvPath = __DIR__ . '/courses.csv';
$handle = fopen($csvPath, 'r');

echo "Reading CSV file row by row:\n";
echo str_repeat("=", 80) . "\n\n";

$rowNum = 0;
while (($row = fgetcsv($handle)) !== false && $rowNum < 2) {
    echo "ROW $rowNum:\n";
    echo "  Columns: " . count($row) . "\n";
    
    foreach ($row as $i => $cell) {
        $preview = strlen($cell) > 60 ? substr($cell, 0, 60) . '...' : $cell;
        $hasNewlines = strpos($cell, "\n") !== false || strpos($cell, "\r") !== false;
        echo "  [$i] (" . strlen($cell) . " chars" . ($hasNewlines ? ", HAS NEWLINES" : "") . "): $preview\n";
    }
    echo "\n";
    
    if ($rowNum == 1) {
        // Detailed analysis of row 1
        echo "DETAILED ANALYSIS OF DATA ROW:\n";
        $code = trim($row[0]);
        $title = trim($row[1]);
        $dateStr = $row[2]; // Don't trim - preserve structure
        $venueStr = $row[3];
        $fee = trim($row[4]);
        
        echo "\nDATE FIELD:\n";
        $dates = preg_split('/\r\n|\r|\n/', $dateStr);
        echo "  After split: " . count($dates) . " dates\n";
        foreach ($dates as $d => $date) {
            if (!empty(trim($date))) {
                echo "    [$d] '" . trim($date) . "'\n";
            }
        }
        
        echo "\nVENUE FIELD:\n";
        $venues = preg_split('/\r\n|\r|\n/', $venueStr);
        echo "  After split: " . count($venues) . " lines\n";
        foreach ($venues as $v => $vline) {
            if (!empty(trim($vline))) {
                echo "    [$v] '" . trim($vline) . "'\n";
                $venueList = array_filter(array_map('trim', explode(',', trim($vline))));
                foreach ($venueList as $venue) {
                    echo "      - $venue\n";
                }
            }
        }
    }
    
    $rowNum++;
}

fclose($handle);
?>
