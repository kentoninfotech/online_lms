<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\CourseDate;
use App\Models\CourseVenue;
use App\Models\CourseCategory;
use Carbon\Carbon;
use Exception;

class ImportCoursesFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:courses {file : The path to the CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import courses with multiple venues and dates from a CSV file. Creates one course with multiple date/venue combinations.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1;
        }

        try {
            $this->importCourses($filePath);
            $this->info('Courses imported successfully!');
            return 0;
        } catch (Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function importCourses($filePath)
    {
        $file = fopen($filePath, 'r');
        if (!$file) {
            throw new Exception("Could not open file: $filePath");
        }

        // Skip header
        $header = fgetcsv($file);
        $this->info("Processing CSV with columns: " . implode(', ', $header));

        $courseCount = 0;
        $venueCount = 0;
        $processedCourses = [];

        while (($row = fgetcsv($file)) !== false) {
            try {
                if (count($row) < 6) {
                    $this->warn("Skipping incomplete row");
                    continue;
                }

                $code = trim($row[0]);
                $title = trim($row[1]);
                $dateLabel = trim($row[2]);
                $venueName = trim($row[3]);
                $fee = (float)$row[4];
                $currency = trim($row[5] ?? 'NGN');

                $this->info("Processing: $code | $title | $dateLabel | $venueName | $fee $currency");

                // Find or create course (only once per course code)
                $course = null;
                if (!isset($processedCourses[$code])) {
                    $course = Course::where('code', $code)->first();
                    
                    if (!$course) {
                        // Create new course
                        $course = Course::create([
                            'code' => $code,
                            'title' => $title,
                            'category_id' => $this->getDefaultCategory(),
                            'level' => 'intermediate',
                            'fee' => $fee,
                            'currency' => $currency,
                            'is_online' => false,
                            'is_offline' => true,
                            'is_active' => true,
                        ]);
                        $courseCount++;
                        $this->info("  ✓ Created course: $code");
                    }
                    
                    $processedCourses[$code] = $course->id;
                } else {
                    $course = Course::findOrFail($processedCourses[$code]);
                }

                // Find or create course date
                $courseDate = CourseDate::where('course_id', $course->id)
                    ->where('date_label', $dateLabel)
                    ->first();

                if (!$courseDate) {
                    // Try to parse the date label
                    $dates = $this->parseDateLabel($dateLabel);
                    
                    $courseDate = CourseDate::create([
                        'course_id' => $course->id,
                        'start_date' => $dates['start'],
                        'end_date' => $dates['end'],
                        'date_label' => $dateLabel,
                        'sequence' => CourseDate::where('course_id', $course->id)->count() + 1,
                    ]);
                    $this->info("  ✓ Created course date: $dateLabel");
                }

                // Create or update course venue with fee
                $venue = CourseVenue::where('course_date_id', $courseDate->id)
                    ->where('venue_name', $venueName)
                    ->first();

                if (!$venue) {
                    $venue = CourseVenue::create([
                        'course_date_id' => $courseDate->id,
                        'venue_name' => $venueName,
                        'country' => $this->getCountryFromVenue($venueName),
                        'fee' => $fee,
                        'sequence' => CourseVenue::where('course_date_id', $courseDate->id)->count() + 1,
                    ]);
                    $venueCount++;
                    $this->info("    ✓ Created venue: $venueName - $fee $currency");
                } else {
                    // Update fee if different
                    if ($venue->fee != $fee) {
                        $venue->update(['fee' => $fee]);
                        $this->info("    ✓ Updated venue fee: $venueName - $fee $currency");
                    }
                }

            } catch (Exception $e) {
                $this->warn("Error processing row: " . $e->getMessage());
                continue;
            }
        }

        fclose($file);

        $this->info("\n=== Import Summary ===");
        $this->info("Courses created: $courseCount");
        $this->info("Venues created: $venueCount");
    }

    /**
     * Parse date label and return start and end dates
     * Expects format like "13 - 17 Apr., 2026"
     */
    private function parseDateLabel($dateLabel)
    {
        try {
            // Extract parts: "13 - 17 Apr., 2026"
            if (preg_match('/(\d+)\s*-\s*(\d+)\s+(\w+)\.,?\s*(\d{4})/', $dateLabel, $matches)) {
                $startDay = (int)$matches[1];
                $endDay = (int)$matches[2];
                $month = $matches[3];
                $year = (int)$matches[4];

                $startDate = Carbon::createFromFormat('d M Y', "$startDay $month $year");
                $endDate = Carbon::createFromFormat('d M Y', "$endDay $month $year");

                return [
                    'start' => $startDate->toDateString(),
                    'end' => $endDate->toDateString(),
                ];
            }
        } catch (Exception $e) {
            $this->warn("Could not parse date: $dateLabel");
        }

        // Default to today and 5 days from now
        return [
            'start' => Carbon::now()->toDateString(),
            'end' => Carbon::now()->addDays(5)->toDateString(),
        ];
    }

    /**
     * Extract country from venue name
     */
    private function getCountryFromVenue($venueName)
    {
        $venueName = strtolower($venueName);
        
        if (str_contains($venueName, 'usa')) return 'USA';
        if (str_contains($venueName, 'uae') || str_contains($venueName, 'dubai')) return 'UAE';
        if (str_contains($venueName, 'south africa')) return 'South Africa';
        if (str_contains($venueName, 'india')) return 'India';
        if (str_contains($venueName, 'nigeria')) return 'Nigeria';
        
        return 'Nigeria';
    }

    /**
     * Get or create default category
     */
    private function getDefaultCategory()
    {
        $category = CourseCategory::firstOrCreate(
            ['name' => 'Professional Development'],
            ['description' => 'Professional and vocational courses']
        );
        
        return $category->id;
    }
}
