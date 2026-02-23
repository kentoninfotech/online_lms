<?php
// Debug venues extraction

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

$header = array_shift($rows);

// Get the first data row (course 1)
$firstRow = $rows[0];

echo "First Course Row Data:\n";
echo "CODE: " . $firstRow[0] . "\n";
echo "TITLE: " . substr($firstRow[1], 0, 50) . "...\n";
echo "\nDATES (raw):\n";
echo $firstRow[2] . "\n";
echo "\nVENUES (raw):\n";
echo $firstRow[3] . "\n";

// Test splitting logic
echo "\n\n=== DATE SPLITTING ===\n";
$datesString = $firstRow[2];
$dates = array_filter(
    array_map('trim', preg_split('/\r\n|\r|\n/', $datesString)),
    fn($d) => !empty($d)
);
echo "Dates count: " . count($dates) . "\n";
foreach ($dates as $i => $date) {
    echo "  [$i] '$date'\n";
}

echo "\n=== VENUE SPLITTING ===\n";
$venuesString = $firstRow[3];
$venueLines = array_filter(
    array_map('trim', preg_split('/\r\n|\r|\n/', $venuesString)),
    fn($v) => !empty($v)
);
echo "Venue lines count: " . count($venueLines) . "\n";
foreach ($venueLines as $i => $venueLine) {
    echo "  [$i] '$venueLine'\n";
    
    // Now split each line by comma
    $venues = array_map('trim', explode(',', $venueLine));
    foreach ($venues as $vi => $venue) {
        echo "       [$vi] '$venue'\n";
    }
}

echo "\n\n=== MATCHING DATES TO VENUES ===\n";
foreach ($dates as $sequence => $dateLabel) {
    echo "Date [$sequence]: '$dateLabel'\n";
    if (isset($venueLines[$sequence])) {
        $venuesForDate = array_map('trim', explode(',', $venueLines[$sequence]));
        echo "  Venues: " . implode(', ', $venuesForDate) . "\n";
    } else {
        echo "  No venues found for sequence $sequence\n";
    }
}
?>
