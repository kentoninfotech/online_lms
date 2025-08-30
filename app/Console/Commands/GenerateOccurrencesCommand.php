<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lesson;
use App\Services\RecurrenceService;

class GenerateOccurrencesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lessons:generate-occurrences {--days= : Override horizon days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate lesson occurrences up to horizon for all lessons';

    protected RecurrenceService $recurrenceService;

    public function __construct(RecurrenceService $recurrenceService)
    {
        parent::__construct();
        $this->recurrenceService = $recurrenceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $horizonDays = $this->option('days') ? intval($this->option('days')) : null;

        $this->info("Generating occurrences...");

        $count = 0;
        $lessons = Lesson::with('student.subscriptions.plan')->chunk(100, function ($lessons) use ($horizonDays, &$count) {
            foreach ($lessons as $lesson) {
                $this->recurrenceService->generateOccurrences($lesson, $horizonDays);
                $count++;
            }
        });
        // $lessons = Lesson::with('student.subscriptions.plan')->get();
        // foreach ($lessons as $lesson) {
        //     $this->recurrenceService->generateOccurrences($lesson, $horizonDays);
        // }

        $this->info("✅ Generated/updated occurrences for {$count} lessons.");
        return Command::SUCCESS;
    }
}
