<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateCourseDatesVenuesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:generate-dates-venues {--force : Force regeneration for all records}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Generate missing start_date, end_date and venues for courses from date_label field';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting course dates and venues generation...');

        $venues = ['Lagos', 'Abuja', 'Port Harcourt', 'Nasarawa', 'Bauchi'];

        // STEP 1: Process rows with multiple dates (checking for commas)
        $this->info('Step 1: Processing course dates with multiple date labels...');
        $records = DB::table('course_dates')
            ->where('date_label', 'LIKE', '%,%')
            ->get();

        if (!$records->isEmpty()) {
            $progressBar = $this->output->createProgressBar($records->count());

            foreach ($records as $record) {
                $progressBar->advance();

                try {
                    // Example: "02 - 06 Mar., 11 - 15 May, 20 - 24 Jul., 08 - 12 Dec., 2026"
                    $parts = explode(',', $record->date_label);
                    $year = trim(end($parts)); // Get 2026
                    array_pop($parts); // Remove the year from the array

                    $shuffledVenues = $venues;
                    shuffle($shuffledVenues);

                    foreach ($parts as $index => $dateSegment) {
                        $dateSegment = trim($dateSegment); // e.g., "02 - 06 Mar."

                        // Extract start day, end day, and month
                        // Regex to capture: (Day1) - (Day2) (Month)
                        preg_match('/(\d+)\s*-\s*(\d+)\s*([a-zA-Z.]+)/', $dateSegment, $matches);

                        if (count($matches) === 4) {
                            $startDay = $matches[1];
                            $endDay = $matches[2];
                            $month = $matches[3];

                            $startDate = Carbon::parse("$startDay $month $year")->format('Y-m-d');
                            $endDate = Carbon::parse("$endDay $month $year")->format('Y-m-d');

                            if ($index === 0) {
                                // UPDATE THE EXISTING ROW
                                DB::table('course_dates')->where('id', $record->id)->update([
                                    'start_date' => $startDate,
                                    'end_date'   => $endDate,
                                ]);
                                $currentId = $record->id;
                            } else {
                                // CREATE NEW ROWS
                                $currentId = DB::table('course_dates')->insertGetId([
                                    'course_id'  => $record->course_id,
                                    'sequence'   => $record->sequence,
                                    'date_label' => $record->date_label,
                                    'start_date' => $startDate,
                                    'end_date'   => $endDate,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }

                            // 2. VENUE LOGIC
                            // Use the shuffled venues array to ensure uniqueness per course date
                            $venueName = $shuffledVenues[$index % count($shuffledVenues)];

                            DB::table('course_venues')->updateOrInsert(
                                ['course_date_id' => $currentId],
                                [
                                    'venue_name' => $venueName,
                                    'updated_at' => now(),
                                    'created_at' => now(),
                                ]
                            );
                        }
                    }
                } catch (\Exception $e) {
                    $this->error("Error processing record ID {$record->id}: " . $e->getMessage());
                }
            }
            $progressBar->finish();
            $this->newLine();
        } else {
            $this->info('No course dates with multiple date labels found.');
        }

        // STEP 2: Assign venues to all course_dates records that don't have a corresponding venue
        $this->info('Step 2: Assigning venues to date records without venues...');
        
        // Get all course_dates that don't have a corresponding course_venues record
        $datesToAssignVenues = DB::table('course_dates')
            ->leftJoin('course_venues', 'course_dates.id', '=', 'course_venues.course_date_id')
            ->whereNull('course_venues.id')
            ->select('course_dates.*')
            ->orderBy('course_dates.course_id')
            ->orderBy('course_dates.id')
            ->get();

        if (!$datesToAssignVenues->isEmpty()) {
            $this->info("Found {$datesToAssignVenues->count()} course dates without venues.");
            
            // Group by course_id
            $groupedByCourse = $datesToAssignVenues->groupBy('course_id');
            $progressBar2 = $this->output->createProgressBar($datesToAssignVenues->count());

            foreach ($groupedByCourse as $courseId => $courseDates) {
                // Shuffle venues for this course to ensure randomness
                $shuffledVenues = $venues;
                shuffle($shuffledVenues);

                $venueIndex = 0;
                foreach ($courseDates as $dateRecord) {
                    $progressBar2->advance();

                    try {
                        // Assign venue, cycling through the list if necessary
                        $venueName = $shuffledVenues[$venueIndex % count($shuffledVenues)];
                        $venueIndex++;

                        DB::table('course_venues')->insert([
                            'course_date_id' => $dateRecord->id,
                            'venue_name' => $venueName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } catch (\Exception $e) {
                        $this->error("Error assigning venue to course_date ID {$dateRecord->id}: " . $e->getMessage());
                    }
                }
            }
            $progressBar2->finish();
            $this->newLine();
        } else {
            $this->info('All course dates already have corresponding venues.');
        }

        $this->info('✅ Course dates and venues generation completed successfully!');

        return Command::SUCCESS;
    }
}
