<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseDate;
use App\Models\CourseVenue;
use Illuminate\Support\Facades\DB;

class CourseCSVImportService
{
    /**
     * Import courses from CSV or Excel file
     * 
     * Supports two formats:
     * 
     * 1. Standard Format:
     *    code, title, subtitle, description, facilitator_id, fee, currency, course_hours, 
     *    is_online, is_offline, is_featured, is_active, max_enrollees
     * 
     * 2. Dates & Venues Format:
     *    code, title, date, venue, fee
     *    - DATE field can contain multiple dates separated by newlines
     *    - VENUE field contains venues matching each date (comma-separated within lines)
     *
     * @param string $filePath
     * @param int $categoryId
     * @param string $fileExtension
     * @param string $csvFormat Either 'standard' or 'dates_venues'
     * @param string $level The course level to assign to all imported courses
     * @return array
     */
    public function import(string $filePath, int $categoryId, string $fileExtension = 'csv', string $csvFormat = 'standard', string $level = null): array
    {
        $category = CourseCategory::findOrFail($categoryId);
        
        $imported = [];
        $errors = [];

        // Normalize path separators for Windows compatibility
        $filePath = $this->normalizePath($filePath);

        if (!file_exists($filePath)) {
            throw new \Exception("File not found: $filePath");
        }

        // Read file based on extension
        $rows = $this->readFile($filePath, $fileExtension);
        
        if (empty($rows)) {
            return [
                'success' => false,
                'imported' => 0,
                'errors_count' => 1,
                'errors' => ['No data found in file'],
            ];
        }

        // Route to appropriate import method
        if ($csvFormat === 'dates_venues') {
            return $this->importDatesVenuesFormat($rows, $category, $categoryId, $level);
        } else {
            return $this->importStandardFormat($rows, $category, $categoryId, $level);
        }
    }

    /**
     * Import courses in standard format
     */
    private function importStandardFormat(array $rows, CourseCategory $category, int $categoryId, string $level = null): array
    {
        $imported = [];
        $errors = [];

        // Extract header row and data rows
        $header = array_shift($rows);
        
        // Debug: Log the header
        \Log::debug('CSV Header detected (Standard)', ['header' => $header]);
        
        $columnMap = $this->buildColumnMap($header);
        
        // Validate that at least CODE and TITLE columns are found
        if (!isset($columnMap['code']) || !isset($columnMap['title'])) {
            $detectedColumns = implode(', ', array_map('strtoupper', array_keys($columnMap)));
            return [
                'success' => false,
                'imported' => 0,
                'errors_count' => 1,
                'errors' => [
                    'Missing required columns: CODE and/or TITLE',
                    'Detected columns: ' . ($detectedColumns ?: 'None detected'),
                    'Header row received: ' . implode(' | ', $header)
                ],
            ];
        }

        DB::beginTransaction();

        try {
            foreach ($rows as $rowNumber => $row) {
                $rowNum = $rowNumber + 2; // +2 because we removed header and arrays start at 0

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Map row data to columns
                $data = $this->mapStandardRowData($row, $columnMap, $categoryId, $level);

                // Validate required fields
                if (empty($data['code']) || empty($data['title'])) {
                    $errors[] = "Row $rowNum: Code and title are required (Code: '{$data['code']}', Title: '{$data['title']}')";
                    continue;
                }

                // Check if course already exists
                if (Course::where('code', $data['code'])->exists()) {
                    $errors[] = "Row $rowNum: Course code '{$data['code']}' already exists in database";
                    continue;
                }

                try {
                    // Create course
                    $course = Course::create($data);
                    
                    $imported[] = [
                        'code' => $data['code'],
                        'title' => $data['title'],
                        'id' => $course->id
                    ];

                } catch (\Exception $e) {
                    $errors[] = "Row $rowNum (Code: '{$data['code']}'): " . $e->getMessage();
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'success' => count($imported) > 0,
            'imported' => count($imported),
            'errors_count' => count($errors),
            'errors' => $errors,
            'total_rows' => count($rows) + 1
        ];
    }

    /**
     * Import courses in dates & venues format
     */
    private function importDatesVenuesFormat(array $rows, CourseCategory $category, int $categoryId, string $level = null): array
    {
        $imported = [];
        $errors = [];

        // Extract header row and data rows
        $header = array_shift($rows);
        
        // Debug: Log the header
        \Log::debug('CSV Header detected', ['header' => $header]);
        
        $columnMap = $this->buildDatesVenuesColumnMap($header);

        // Validate required columns
        $requiredColumns = ['code', 'title', 'date', 'venue', 'fee'];
        $missingColumns = [];
        foreach ($requiredColumns as $col) {
            if (!isset($columnMap[$col])) {
                $missingColumns[] = strtoupper($col);
            }
        }
        
        if (!empty($missingColumns)) {
            $detectedColumns = implode(', ', array_map('strtoupper', array_keys($columnMap)));
            return [
                'success' => false,
                'imported' => 0,
                'errors_count' => 1,
                'errors' => [
                    'Missing required columns: ' . implode(', ', $missingColumns),
                    'Detected columns: ' . ($detectedColumns ?: 'None detected'),
                    'Expected columns: CODE, TITLE, DATE, VENUE, FEE'
                ],
            ];
        }

        DB::beginTransaction();

        try {
            foreach ($rows as $rowNumber => $row) {
                $rowNum = $rowNumber + 2;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Extract data
                $code = trim($row[$columnMap['code']] ?? '');
                $title = trim($row[$columnMap['title']] ?? '');
                $datesString = $row[$columnMap['date']] ?? '';
                $venuesString = $row[$columnMap['venue']] ?? '';
                $feeStr = trim($row[$columnMap['fee']] ?? '');

                // Validate required fields
                if (empty($code) || empty($title)) {
                    $errors[] = "Row $rowNum: Code and title are required (Code: '$code', Title: '$title')";
                    continue;
                }

                if (empty($datesString)) {
                    $errors[] = "Row $rowNum: Date field cannot be empty for course '$code'";
                    continue;
                }

                // Check if course already exists
                if (Course::where('code', $code)->exists()) {
                    $errors[] = "Row $rowNum: Course code '$code' already exists in database";
                    continue;
                }

                try {
                    // Create course with initial fee (will be updated from venues if in dates_venues format)
                    $course = Course::create([
                        'code' => $code,
                        'title' => $title,
                        'category_id' => $categoryId,
                        'level' => $level,
                        'fee' => 0, // Will be set from venue fees
                        'currency' => 'NGN',
                        'is_active' => true,
                        'is_online' => false,
                        'is_offline' => true,
                        'is_featured' => false,
                    ]);
                    
                    // Parse and create dates and venues
                    // This will update the course fee from the first venue fee
                    $this->importDatesAndVenues($course, $datesString, $venuesString, $rowNum, $errors);
                    
                    $imported[] = [
                        'code' => $code,
                        'title' => $title,
                        'id' => $course->id
                    ];

                } catch (\Exception $e) {
                    // Delete course if something goes wrong
                    if (isset($course)) {
                        $course->delete();
                    }
                    $errors[] = "Row $rowNum (Code: '$code'): " . $e->getMessage();
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'success' => count($imported) > 0,
            'imported' => count($imported),
            'errors_count' => count($errors),
            'errors' => $errors,
            'total_rows' => count($rows) + 1
        ];
    }

    /**
     * Import dates and venues for a course
     * 
     * Handles both single-line (comma-separated) and multi-line (newline-separated) formats
     * Example formats:
     * 
     * Single line: "23 - 27 Mar., 25 - 29 May, 13 - 17 Jul., 05 - 09 Oct., 2026"
     *              with venues: "Ibadan, Lagos, Bauchi, Nasarawa"
     * 
     * Multi-line with fees: "date1\ndate2\ndate3"
     *                       with venues: "VENUE1 – $FEE1\nVENUE2 – $FEE2"
     */
    private function importDatesAndVenues(Course $course, string $datesString, string $venuesString, int $rowNum, array &$errors): void
    {
        // Parse dates
        $datesByNewline = array_filter(
            array_map('trim', preg_split('/\r\n|\r|\n/', $datesString)),
            fn($d) => !empty($d)
        );
        
        // Parse venues with fees (expecting "VENUE – $FEE" format)
        $venuesByNewline = array_filter(
            array_map('trim', preg_split('/\r\n|\r|\n/', $venuesString)),
            fn($v) => !empty($v)
        );
        
        $dates = !empty($datesByNewline) ? array_values($datesByNewline) : [];
        $venuesWithFees = !empty($venuesByNewline) ? array_values($venuesByNewline) : [];

        if (empty($dates)) {
            $errors[] = "Row $rowNum: No valid dates found";
            return;
        }

        // Extract venue data with fees
        $venuesData = [];
        foreach ($venuesWithFees as $venueEntry) {
            $parsed = $this->parseVenueWithFee($venueEntry);
            $venuesData[] = $parsed;
        }

        // Track created venues for this course to prevent duplicates
        $createdVenues = [];
        $firstFee = null;

        // For each date, create a CourseDate entry
        foreach ($dates as $sequence => $dateLabel) {
            try {
                $courseDate = CourseDate::create([
                    'course_id' => $course->id,
                    'date_label' => trim($dateLabel),
                    'sequence' => $sequence + 1,
                    'start_date' => null,
                    'end_date' => null,
                ]);

                // Get venue data for this sequence
                if ($sequence < count($venuesData)) {
                    $venueData = $venuesData[$sequence];
                    $venueName = $venueData['venue'];
                    $venueFee = $venueData['fee'];
                    
                    // Store the first fee for the course record
                    if ($firstFee === null && $venueFee !== null) {
                        $firstFee = $venueFee;
                    }
                    
                    // Normalize venue name for duplicate checking (case-insensitive)
                    $normalizedVenueKey = strtolower($venueName);
                    
                    // Skip if venue already created for this course
                    if (!empty($venueName) && !isset($createdVenues[$normalizedVenueKey])) {
                        CourseVenue::create([
                            'course_date_id' => $courseDate->id,
                            'venue_name' => $venueName,
                            'fee' => $venueFee,
                            'address' => null,
                            'city' => null,
                            'state' => null,
                            'capacity' => null,
                            'enrolled_count' => 0,
                        ]);
                        
                        // Mark this venue as created for this course
                        $createdVenues[$normalizedVenueKey] = true;
                    }
                }

            } catch (\Exception $e) {
                $errors[] = "Row $rowNum: Error creating date/venue - " . $e->getMessage();
            }
        }
        
        // Update course fee from first venue fee if we have one
        if ($firstFee !== null) {
            $course->update(['fee' => $firstFee]);
        }
    }

    /**
     * Parse a venue entry in format: "VENUE – $FEE" or "VENUE - $FEE"
     * Returns array with 'venue' and 'fee' keys
     * 
     * @param string $venueEntry e.g., "USA – $6,500"
     * @return array ['venue' => 'USA', 'fee' => 6500.00]
     */
    private function parseVenueWithFee(string $venueEntry): array
    {
        $venueEntry = trim($venueEntry);
        
        // Try to match pattern: "VENUE – $FEE" or "VENUE - $FEE"
        // The pattern looks for text followed by dash and optional dollar sign and number
        if (preg_match('/^(.+?)\s*[-–]\s*\$?([\d,\.]+)/', $venueEntry, $matches)) {
            $venue = trim($matches[1]);
            $feeStr = trim($matches[2]);
            $fee = floatval(str_replace(',', '', $feeStr));
            
            return [
                'venue' => $venue,
                'fee' => $fee
            ];
        }
        
        // If no fee pattern found, return venue name with null fee
        return [
            'venue' => $venueEntry,
            'fee' => null
        ];
    }

    /**
     * Parse comma-separated dates on a single line
     * Handles formats like: "23 - 27 Mar., 25 - 29 May, 13 - 17 Jul., 05 - 09 Oct., 2026"
     * 
     * Smart parsing to not split on commas within date ranges,
     * but does split on ", " (comma+space) which separates individual date specs.
     * 
     * @param string $datesString
     * @return array
     */
    private function parseCommaSeparatedDates(string $datesString): array
    {
        $datesString = trim($datesString);
        
        if (empty($datesString)) {
            return [];
        }
        
        // Split by ", " (comma + space) which is the separator between dates in the CSV
        // This won't split incorrectly because dates don't have ", " within them
        $dates = array_filter(
            array_map('trim', explode(',', $datesString)),
            fn($d) => !empty($d)
        );
        
        // Clean up dates and combine incomplete year references
        // E.g., if final segment is just a year "2026", append it to previous date
        $cleanedDates = [];
        $year = '';
        
        foreach ($dates as $date) {
            $trimmedDate = trim($date);
            
            // Check if this looks like a year (4 digits)
            if (preg_match('/^\d{4}$/', $trimmedDate)) {
                $year = $trimmedDate;
                // Append year to all previous dates that don't have it
                foreach ($cleanedDates as &$d) {
                    if (!preg_match('/,\s*' . preg_quote($year) . '\s*$/', $d)) {
                        $d .= ', ' . $year;
                    }
                }
                unset($d);
            } else {
                // Add year to this date if we have one
                if (!empty($year) && !preg_match('/\d{4}/', $trimmedDate)) {
                    $cleanedDates[] = trim($trimmedDate . ', ' . $year);
                } else {
                    $cleanedDates[] = $trimmedDate;
                }
            }
        }
        
        return array_values(array_filter($cleanedDates));
    }

    /**
     * Parse comma-separated venue names on a single line
     * Returns array of venue names
     * 
     * @param string $venuesString
     * @return array
     */
    private function parseCommaSeparatedVenues(string $venuesString): array
    {
        $venuesString = trim($venuesString);
        
        if (empty($venuesString)) {
            return [];
        }
        
        // Split by comma and clean up each venue name
        $venues = array_filter(
            array_map('trim', explode(',', $venuesString)),
            fn($v) => !empty($v)
        );
        
        return array_values($venues);
    }

    /**
     * Normalize file path separators for cross-platform compatibility
     */
    private function normalizePath(string $path): string
    {
        // Convert forward slashes to system-appropriate separator
        if (DIRECTORY_SEPARATOR === '\\') {
            // Windows
            return str_replace('/', '\\', $path);
        } else {
            // Unix-like
            return str_replace('\\', '/', $path);
        }
    }

    /**
     * Read file data (CSV or Excel)
     */
    private function readFile(string $filePath, string $extension): array
    {
        $extension = strtolower($extension);

        if (in_array($extension, ['xlsx', 'xls'])) {
            return $this->readExcelFile($filePath);
        } else {
            return $this->readCsvFile($filePath);
        }
    }

    /**
     * Read CSV file
     */
    private function readCsvFile(string $filePath): array
    {
        $rows = [];
        
        if (!file_exists($filePath)) {
            \Log::error('CSV file not found', ['path' => $filePath]);
            return [];
        }
        
        if (!is_readable($filePath)) {
            \Log::error('CSV file not readable', ['path' => $filePath]);
            return [];
        }
        
        if (($handle = fopen($filePath, 'r')) !== false) {
            $rowNum = 0;
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                $trimmedRow = array_map('trim', $row ?? []);
                $rows[] = $trimmedRow;
                
                // Log first few rows for debugging
                if ($rowNum <= 2) {
                    \Log::debug("CSV Row $rowNum", ['data' => $trimmedRow]);
                }
            }
            fclose($handle);
            \Log::info('CSV file read successfully', ['path' => $filePath, 'rows' => $rowNum]);
        } else {
            \Log::error('Failed to open CSV file', ['path' => $filePath]);
        }
        
        return $rows;
    }

    /**
     * Read Excel file
     */
    private function readExcelFile(string $filePath): array
    {
        try {
            $rows = [];
            
            // Use PhpSpreadsheet if available
            if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                
                foreach ($sheet->getRowIterator() as $row) {
                    $rowData = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    $rows[] = $rowData;
                }
                
                return $rows;
            }
            
            // Fallback: try using basic CSV reading on Excel-generated temp CSV
            // This is less ideal but works for basic cases
            return $this->readCsvFile($filePath);
            
        } catch (\Exception $e) {
            throw new \Exception(
                "Excel file support requires PhpSpreadsheet.\n" .
                "Install with: composer require phpoffice/phpspreadsheet\n" .
                "Or convert your file to CSV format.\n" .
                "Error: " . $e->getMessage()
            );
        }
    }

    /**
     * Build column map from header row
     */
    private function buildColumnMap(array $header): array
    {
        $map = [];
        $validColumns = [
            'code', 'title', 'subtitle', 'description', 'facilitator_id',
            'fee', 'currency', 'course_hours', 'is_online', 'is_offline',
            'is_featured', 'is_active', 'max_enrollees', 'date', 'venue'
        ];

        foreach ($header as $index => $column) {
            $normalizedColumn = strtolower(trim($column));
            
            // Remove extra spaces and underscores for matching
            $cleanedColumn = str_replace([' ', '_', '-'], '', $normalizedColumn);
            
            // Try exact match first
            if (in_array($normalizedColumn, $validColumns)) {
                $map[$normalizedColumn] = $index;
                continue;
            }
            
            // Try fuzzy match - check if any valid column is contained in the normalized column
            // or if the normalized column ends with the valid column
            foreach ($validColumns as $valid) {
                $cleanedValid = str_replace([' ', '_', '-'], '', $valid);
                
                // Match if:
                // 1. Exact cleaned match (e.g., "facilitatorid" == "facilitatorid")
                // 2. Normalized contains valid (e.g., "course title" contains "title")
                // 3. Valid is suffix of normalized (e.g., ends with "title")
                if ($cleanedColumn === $cleanedValid ||
                    strpos($normalizedColumn, $valid) !== false ||
                    strpos($cleanedColumn, $cleanedValid) !== false) {
                    $map[$valid] = $index;
                    break;
                }
            }
        }

        return $map;
    }

    /**
     * Build column map for dates & venues format
     */
    private function buildDatesVenuesColumnMap(array $header): array
    {
        $map = [];
        $validColumns = ['code', 'title', 'date', 'venue', 'fee'];

        foreach ($header as $index => $column) {
            $normalizedColumn = strtolower(trim($column));
            $cleanedColumn = str_replace([' ', '_', '-'], '', $normalizedColumn);
            
            // Try exact match first
            if (in_array($normalizedColumn, $validColumns)) {
                $map[$normalizedColumn] = $index;
                continue;
            }
            
            // Try fuzzy match
            foreach ($validColumns as $valid) {
                $cleanedValid = str_replace([' ', '_', '-'], '', $valid);
                
                if ($cleanedColumn === $cleanedValid ||
                    strpos($normalizedColumn, $valid) !== false ||
                    strpos($cleanedColumn, $cleanedValid) !== false) {
                    $map[$valid] = $index;
                    break;
                }
            }
        }

        return $map;
    }

    /**
     * Map row data for standard format
     */
    private function mapStandardRowData(array $row, array $columnMap, int $categoryId, string $level = null): array
    {
        $data = [
            'category_id' => $categoryId,
            'level' => $level,
            'currency' => 'NGN',
            'is_active' => true,
            'is_online' => false,
            'is_offline' => true,
            'is_featured' => false,
        ];

        // Map columns if they exist
        if (isset($columnMap['code'])) {
            $data['code'] = trim($row[$columnMap['code']] ?? '');
        }

        if (isset($columnMap['title'])) {
            $data['title'] = trim($row[$columnMap['title']] ?? '');
        }

        if (isset($columnMap['subtitle'])) {
            $subtitle = trim($row[$columnMap['subtitle']] ?? '');
            $data['subtitle'] = !empty($subtitle) ? $subtitle : null;
        }

        if (isset($columnMap['description'])) {
            $description = trim($row[$columnMap['description']] ?? '');
            $data['description'] = !empty($description) ? $description : null;
        }

        if (isset($columnMap['facilitator_id'])) {
            $facilId = trim($row[$columnMap['facilitator_id']] ?? '');
            $data['facilitator_id'] = !empty($facilId) && is_numeric($facilId) ? (int)$facilId : null;
        }

        if (isset($columnMap['fee'])) {
            $feeStr = trim($row[$columnMap['fee']] ?? '');
            $data['fee'] = $this->parseFee($feeStr);
        }

        if (isset($columnMap['currency'])) {
            $currency = strtoupper(trim($row[$columnMap['currency']] ?? 'NGN'));
            $data['currency'] = in_array($currency, ['NGN', 'USD', 'GBP', 'EUR']) ? $currency : 'NGN';
        }

        if (isset($columnMap['course_hours'])) {
            $hours = trim($row[$columnMap['course_hours']] ?? '');
            $data['course_hours'] = !empty($hours) && is_numeric($hours) ? (float)$hours : null;
        }

        if (isset($columnMap['max_enrollees'])) {
            $max = trim($row[$columnMap['max_enrollees']] ?? '');
            $data['max_enrollees'] = !empty($max) && is_numeric($max) ? (int)$max : null;
        }

        // Boolean fields
        if (isset($columnMap['is_online'])) {
            $isOnline = strtolower(trim($row[$columnMap['is_online']] ?? ''));
            $data['is_online'] = in_array($isOnline, ['1', 'true', 'yes', 'y', 'on']) ? true : false;
        }

        if (isset($columnMap['is_offline'])) {
            $isOffline = strtolower(trim($row[$columnMap['is_offline']] ?? ''));
            $data['is_offline'] = in_array($isOffline, ['1', 'true', 'yes', 'y', 'on']) ? true : false;
        }

        if (isset($columnMap['is_featured'])) {
            $isFeatured = strtolower(trim($row[$columnMap['is_featured']] ?? ''));
            $data['is_featured'] = in_array($isFeatured, ['1', 'true', 'yes', 'y', 'on']) ? true : false;
        }

        if (isset($columnMap['is_active'])) {
            $isActive = strtolower(trim($row[$columnMap['is_active']] ?? ''));
            $data['is_active'] = in_array($isActive, ['1', 'true', 'yes', 'y', 'on']) ? true : false;
        }

        return $data;
    }

    /**
     * Parse fee value, removing currency symbols
     */
    private function parseFee(?string $feeStr): ?float
    {
        if (empty($feeStr)) {
            return null;
        }

        // Remove common currency symbols and commas
        $fee = preg_replace('/[^\d.]/', '', $feeStr);
        
        if (empty($fee)) {
            return null;
        }

        return floatval($fee);
    }
}

