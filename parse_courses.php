<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$inputFile = 'c:/Users/Ogochukwu/Downloads/1.xlsx';
$spreadsheet = IOFactory::load($inputFile);
$sheet = $spreadsheet->getActiveSheet();

$rows = [];
$dateIndex = 0;
$venueIndex = 0;
$feeIndex = 0;

// Read the header
foreach ($sheet->getRowIterator(1, 1) as $row) {
    $cellIterator = $row->getCellIterator();
    $headerIndex = 0;
    foreach ($cellIterator as $cell) {
        $value = $cell->getValue();
        if (stripos($value, 'DATE') !== false) {
            $dateIndex = $headerIndex;
        } elseif (stripos($value, 'VENUE') !== false) {
            $venueIndex = $headerIndex;
        } elseif (stripos($value, 'FEE') !== false && $feeIndex === 0) {
            $feeIndex = $headerIndex;
        }
        $headerIndex++;
    }
}

// Read data rows
$newRows = [];
foreach ($sheet->getRowIterator(2) as $row) {
    $cellIterator = $row->getCellIterator();
    $rowData = [];
    $cellIndex = 0;
    foreach ($cellIterator as $cell) {
        $rowData[$cellIndex] = $cell->getValue();
        $cellIndex++;
    }
    
    // Parse dates and venues
    $datesStr = trim($rowData[$dateIndex] ?? '');
    $venuesStr = trim($rowData[$venueIndex] ?? '');
    
    // Split dates by newline
    $dates = array_filter(array_map('trim', explode("\n", $datesStr)));
    
    // Parse venues and fees from "VENUE – $FEE" format
    $venuesAndFees = array_filter(array_map('trim', explode("\n", $venuesStr)));
    
    // Create a row for each date/venue/fee combination
    foreach ($dates as $idx => $date) {
        if (isset($venuesAndFees[$idx])) {
            $venueFee = $venuesAndFees[$idx];
            
            // Extract venue and fee
            if (preg_match('/^(.+?)\s*[-–]\s*\$?([\d,\.]+)/', $venueFee, $matches)) {
                $venue = trim($matches[1]);
                $fee = floatval(str_replace(',', '', $matches[2]));
                
                $newRow = $rowData;
                $newRow[$dateIndex] = $date;
                $newRow[$venueIndex] = $venue;
                $newRow[$feeIndex] = $fee;
                
                $newRows[] = $newRow;
            }
        }
    }
}

// Write output CSV
$outputFile = 'c:/Users/Ogochukwu/Downloads/1_parsed.csv';
$handle = fopen($outputFile, 'w');

// Write header
$header = [];
foreach ($sheet->getRowIterator(1, 1) as $row) {
    $cellIterator = $row->getCellIterator();
    foreach ($cellIterator as $cell) {
        $header[] = $cell->getValue();
    }
    break;
}
fputcsv($handle, $header);

// Write rows
foreach ($newRows as $row) {
    fputcsv($handle, $row);
}
fclose($handle);

echo "Parsed CSV created: $outputFile\n";
echo "Total rows: " . count($newRows) . "\n";

// Display sample
echo "\nFirst 5 rows:\n";
$fp = fopen($outputFile, 'r');
for ($i = 0; $i < 6; $i++) {
    $row = fgetcsv($fp);
    if ($row) {
        echo implode(' | ', $row) . "\n";
    }
}
fclose($fp);
?>
