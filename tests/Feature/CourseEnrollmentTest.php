<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollee;
use App\Models\CourseDate;
use App\Models\CourseVenue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->category = CourseCategory::factory()->create();
        $this->course = Course::factory()
            ->for($this->category)
            ->create(['fee' => 50000]);
        
        $this->date = CourseDate::factory()
            ->for($this->course)
            ->create();
        
        $this->venue = CourseVenue::factory()
            ->for($this->date)
            ->create();
        
        $this->user = User::factory()->create();
    }

    public function test_user_can_view_course_listing()
    {
        $response = $this->get(route('courses.index'));
        
        $response->assertStatus(200);
        $response->assertViewHas('courses');
    }

    public function test_user_can_view_course_details()
    {
        $response = $this->get(route('courses.show', $this->course));
        
        $response->assertStatus(200);
        $response->assertViewHas('course', $this->course);
    }

    public function test_user_can_view_enrollment_form()
    {
        $this->actingAs($this->user);
        
        $response = $this->get(route('courses.enroll', $this->course));
        
        $response->assertStatus(200);
        $response->assertViewHas('course', $this->course);
    }

    public function test_user_can_enroll_in_course()
    {
        $this->actingAs($this->user);
        
        $response = $this->post(route('courses.enroll.store', $this->course), [
            'course_date_id' => $this->date->id,
            'course_venue_id' => $this->venue->id,
        ]);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('course_enrollees', [
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
        ]);
    }

    public function test_user_cannot_enroll_twice_in_same_course()
    {
        $this->actingAs($this->user);
        
        // First enrollment
        $this->post(route('courses.enroll.store', $this->course), [
            'course_date_id' => $this->date->id,
            'course_venue_id' => $this->venue->id,
        ]);
        
        // Second enrollment attempt
        $response = $this->post(route('courses.enroll.store', $this->course), [
            'course_date_id' => $this->date->id,
            'course_venue_id' => $this->venue->id,
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_venue_capacity_is_enforced()
    {
        $this->venue->update(['capacity' => 1]);
        
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        // First user enrolls
        $this->actingAs($user1);
        $this->post(route('courses.enroll.store', $this->course), [
            'course_date_id' => $this->date->id,
            'course_venue_id' => $this->venue->id,
        ]);
        
        // Second user tries to enroll
        $this->actingAs($user2);
        $response = $this->post(route('courses.enroll.store', $this->course), [
            'course_date_id' => $this->date->id,
            'course_venue_id' => $this->venue->id,
        ]);
        
        $response->assertSessionHasErrors();
    }

    public function test_user_can_view_their_enrollments()
    {
        // Create enrollment
        CourseEnrollee::factory()
            ->for($this->user)
            ->for($this->course)
            ->for($this->date)
            ->for($this->venue)
            ->create();
        
        $this->actingAs($this->user);
        
        $response = $this->get(route('courses.my-enrollments'));
        
        $response->assertStatus(200);
        $response->assertViewHas('enrollments');
    }
}
