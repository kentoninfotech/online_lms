<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\CourseEnrollee;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\CourseContentCompletion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrollment_progress_calculation()
    {
        $enrollment = CourseEnrollee::factory()->create();
        $course = $enrollment->course;
        
        // Create 5 required contents
        for ($i = 0; $i < 5; $i++) {
            $content = CourseContent::factory()
                ->for($course)
                ->create(['is_required' => true]);
            
            CourseContentCompletion::factory()
                ->for($enrollment)
                ->for($content)
                ->create(['is_completed' => $i < 3]); // 3 completed, 2 not
        }
        
        $progress = $enrollment->calculateProgressPercentage();
        
        $this->assertEquals(60, $progress); // 3/5 = 60%
    }

    public function test_completion_tracks_time_spent()
    {
        $completion = CourseContentCompletion::factory()->create();
        
        $completion->markStarted();
        sleep(2);
        $completion->markCompleted();
        
        $this->assertTrue($completion->is_completed);
        $this->assertNotNull($completion->completed_at);
    }

    public function test_optional_content_not_counted_in_progress()
    {
        $enrollment = CourseEnrollee::factory()->create();
        $course = $enrollment->course;
        
        // Create 1 required, 1 optional
        $required = CourseContent::factory()
            ->for($course)
            ->create(['is_required' => true]);
        
        $optional = CourseContent::factory()
            ->for($course)
            ->create(['is_required' => false]);
        
        // Complete the optional one only
        CourseContentCompletion::factory()
            ->for($enrollment)
            ->for($optional)
            ->create(['is_completed' => true]);
        
        CourseContentCompletion::factory()
            ->for($enrollment)
            ->for($required)
            ->create(['is_completed' => false]);
        
        $progress = $enrollment->calculateProgressPercentage();
        
        $this->assertEquals(0, $progress); // Only required contents count
    }
}
