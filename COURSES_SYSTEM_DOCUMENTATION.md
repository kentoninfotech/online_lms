# Courses Management System - Complete Documentation

## Overview
This is a comprehensive online learning management system with support for:
- **Offline & Online Courses** - Both modes available
- **Facilitator Management** - Different from existing instructors
- **Student Enrollments** - Different from existing students
- **Online Learning Content** - Text, PDF, Excel, Word, PowerPoint
- **Quiz System** - Multiple attempts, grading, leaderboards
- **Live Sessions** - Zoom/Meet/Teams integration
- **Discussion Forums** - Course discussions with threading
- **Video Recording** - Auto-tracking of attendance
- **Certificate Generation** - Upon course completion
- **Payment Processing** - One-time payment per course

## Database Schema

### Core Tables

#### `course_categories`
- `id` - Primary key
- `name` - Category name (e.g., "Business", "Technology")
- `slug` - URL-friendly slug
- `color` - Hex color for theme
- `icon` - Icon reference
- `is_active` - Boolean for visibility
- `sort_order` - Display order

#### `facilitators`
- `id` - Primary key
- `user_id` - Link to User model (different from instructors)
- `name`, `email`, `phone` - Contact info
- `bio` - Professional biography
- `profile_image` - Profile photo
- `qualification` - Credentials
- `expertise` - Areas of expertise
- `is_verified` - Admin verification flag
- `is_active` - Active status

#### `courses`
- `id` - Primary key
- `code` - Unique course code (for CSV import)
- `title`, `subtitle` - Course naming
- `description` - Full course description (richtext)
- `category_id` - Foreign key to categories
- `facilitator_id` - Foreign key to facilitators
- `fee` - Course cost
- `currency` - Pricing currency (NGN, USD, GBP, EUR)
- `course_hours` - Total course duration in hours
- `is_online`, `is_offline` - Mode flags
- `is_featured` - Featured on homepage
- `is_active` - Active flag
- `featured_image` - Course cover image
- `max_enrollees` - Enrollment cap (optional)
- `enrolled_count` - Current enrollment count

#### `course_dates`
- `id` - Primary key
- `course_id` - Foreign key
- `start_date`, `end_date` - Date range
- `date_label` - Human-readable label (e.g., "13 - 17 Apr., 2026")
- `sequence` - Order (1st date, 2nd date, etc.)
- `notes` - Optional notes

**Note:** Each course has 4 dates as per CSV format

#### `course_venues`
- `id` - Primary key
- `course_date_id` - Foreign key
- `venue_name` - Location name
- `address`, `city`, `state`, `country` - Location details
- `latitude`, `longitude` - GPS coordinates
- `capacity` - Max attendees
- `enrolled_count` - Current enrollments
- `notes` - Venue notes

**Note:** Each date can have multiple venues

#### `course_enrollees`
- `id` - Primary key
- `user_id` - Student enrolling
- `course_id` - Course enrolled in
- `course_date_id` - Specific date
- `course_venue_id` - Specific venue
- `status` - pending|active|completed|cancelled|suspended
- `payment_status` - pending|completed|failed|refunded
- `amount_paid` - Payment amount
- `transaction_id` - Payment reference
- `payment_date` - Payment timestamp
- `enrolled_at` - Enrollment timestamp
- `completed_at` - Completion timestamp
- `progress_percentage` - Learning progress (0-100)
- `notes` - Admin notes

### Online Learning Tables

#### `course_contents`
- `id` - Primary key
- `course_id` - Foreign key
- `title` - Content title
- `description` - Content description
- `content_type` - text|pdf|excel|word|powerpoint|video|link|image
- `content` - HTML/text content
- `file_path` - Path to uploaded file
- `duration_minutes` - Time to complete
- `sequence` - Display order
- `section_id` - Group contents into sections
- `is_published` - Publication status
- `is_required` - Must-complete flag

#### `course_content_completions`
- `id` - Primary key
- `course_enrollee_id` - Student-course link
- `course_content_id` - Content item
- `time_spent_minutes` - Learning time tracked
- `is_completed` - Completion flag
- `completed_at` - Completion timestamp
- `started_at` - Start timestamp
- `progress_percentage` - Item progress

#### `course_quizzes`
- `id` - Primary key
- `course_id` - Foreign key
- `title` - Quiz name
- `description` - Quiz description
- `total_questions` - Question count
- `passing_score` - Pass threshold (%)
- `time_limit_minutes` - Time allowed
- `attempts_allowed` - Retry count
- `show_correct_answers` - Answer visibility
- `shuffle_questions` - Random order
- `is_published` - Publication status
- `sequence` - Order in course

#### `quiz_questions`
- `id` - Primary key
- `quiz_id` - Foreign key
- `question` - Question text
- `question_type` - multiple_choice|true_false|short_answer|essay
- `correct_answer` - Answer (JSON format)
- `points` - Points value
- `sequence` - Question order

#### `quiz_answers`
- `id` - Primary key
- `question_id` - Foreign key
- `answer` - Answer code
- `answer_text` - Display text
- `is_correct` - Correctness flag
- `sequence` - Option order

#### `quiz_submissions`
- `id` - Primary key
- `course_enrollee_id` - Student-course link
- `quiz_id` - Quiz taken
- `attempt_number` - Attempt #
- `total_questions` - Questions on attempt
- `correct_answers` - Correct count
- `score` - Percentage (0-100)
- `is_passed` - Pass/fail
- `time_taken_minutes` - Time used
- `submitted_at` - Submission time

### Discussion & Collaboration Tables

#### `course_discussions`
- `id` - Primary key
- `course_id` - Foreign key
- `user_id` - Author
- `title` - Thread title
- `message` - Initial post
- `is_pinned` - Pin to top
- `is_locked` - Prevent replies
- `replies_count` - Reply count

#### `discussion_replies`
- `id` - Primary key
- `discussion_id` - Parent thread
- `user_id` - Author
- `message` - Reply content
- `reply_to_id` - Parent reply (for threading)

### Live Session Tables

#### `course_live_sessions`
- `id` - Primary key
- `course_id` - Foreign key
- `facilitator_id` - Foreign key
- `title` - Session name
- `description` - Session description
- `scheduled_start`, `scheduled_end` - Scheduled times
- `session_type` - zoom|meet|teams|other
- `meeting_link` - Join URL
- `meeting_id`, `meeting_password` - Meeting details
- `status` - scheduled|live|completed|cancelled
- `attendees_count` - Attendance count
- `actual_start`, `actual_end` - Actual times
- `recording_url` - Video recording

#### `live_session_attendances`
- `id` - Primary key
- `live_session_id` - Foreign key
- `user_id` - Attendee
- `joined_at`, `left_at` - Session times
- `duration_minutes` - Attendance duration
- `attendance_status` - present|absent|late|partial

### Payment & Certificate Tables

#### `course_payments`
- `id` - Primary key
- `course_enrollee_id` - Enrollment link
- `user_id` - Student
- `course_id` - Course
- `amount` - Payment amount
- `currency` - Currency code
- `payment_method` - Method (card, bank_transfer, etc.)
- `reference_id` - Payment reference
- `status` - pending|completed|failed|refunded
- `payment_details` - JSON Payment info
- `paid_at` - Payment timestamp

#### `course_certificates`
- `id` - Primary key
- `course_enrollee_id` - Enrollment link
- `certificate_number` - Unique certificate ID
- `issued_at`, `expires_at` - Validity dates
- `file_path` - Certificate PDF path
- `is_revoked` - Revocation flag
- `revoke_reason` - Revocation explanation

### UI & Configuration Tables

#### `course_carousel_images`
- `id` - Primary key
- `image_path` - Image file
- `title`, `description` - Content
- `button_text`, `button_link` - CTA
- `sort_order` - Display order
- `is_active` - Visibility
- `display_from`, `display_until` - Display window

#### `settings` (Extended)
- `primary_color` - Primary theme color (default: #3B82F6 - Blue)
- `secondary_color` - Secondary color (#1E40AF)
- `accent_color` - Accent color (#60A5FA)
- `courses_enabled` - Enable/disable course module
- `online_courses_enabled` - Enable/disable online mode
- `featured_courses_limit` - Max featured courses

## Key Features Implementation

### 1. CSV Bulk Import
**File:** `app/Services/CourseCSVImportService.php`

Expected CSV Format:
```
CODE,COURSE TITLE,DATE,VENUE,FEE
1,"Computer Application","13 - 17 Apr., 2026\n25 - 29 May, 2026","Nasarawa, Lagos\nBauchi, Ibadan","N460,000"
```

Process:
1. Admin selects category
2. Uploads CSV file
3. System parses dates and venues
4. All courses get selected category
5. Returns import summary with errors

### 2. Course Enrollment
Process:
1. User selects course from listing
2. Chooses specific date and venue
3. System checks capacity
4. Creates enrollment in pending status
5. Creates payment record
6. Directs to payment gateway

### 3. Online Learning
Features:
- Timer for cada content (duration_minutes)
- Progress tracking per student
- Content types: Text (HTML), PDF, Excel, Word, PowerPoint, Video
- Completion button shows after timer ends
- Integration with quiz system

### 4. Quiz System
Features:
- Multiple question types: Multiple Choice, True/False, Short Answer, Essay
- Configurable passing score
- Attempt limiting with scoring
- Time limits per quiz
- Shuffle questions option
- Show/hide correct answers

### 5. Live Sessions
Features:
- Zoom/Meet/Teams integration
- Automatic attendance tracking
- Scheduled notifications
- Recording storage
- Post-session replay

### 6. Discussion Forum
Features:
- Threading (replies to replies)
- Pinned announcements
- Locked threads
- Admin moderation
- Deleted post recovery

### 7. Payment System
- Single payment per course (non-subscription)
- Multiple currencies support
- Payment gateway integration ready
- Automatic receipt generation
- Failed payment handling

### 8. Certificates
- Auto-issued upon completion
- Unique certificate numbers
- PDF generation
- Revocation capability
- Validity periods

## API Endpoints

### Authentication Required

#### Courses
- `GET /courses` - List all courses
- `GET /courses/category/{category}` - Filter by category
- `GET /course/{course}` - Course details
- `GET /course/{course}/enroll` - Enrollment form
- `POST /course/{course}/enroll` - Submit enrollment

#### Learning
- `GET /course/{course}/learn` - Course dashboard
- `GET /course/{course}/content/{content}` - Load content
- `POST /course/{course}/content/{content}/complete` - Mark complete
- `GET /course/{course}/quiz/{quiz}` - Load quiz
- `POST /course/{course}/quiz/{quiz}/submit` - Submit answers
- `GET /course/{course}/quiz/{quiz}/result/{submission}` - View results

#### Discussions
- `GET /course/{course}/discussions` - List discussions
- `POST /course/{course}/discussions` - Create thread
- `GET /course/{course}/discussions/{discussion}` - View thread
- `POST /course/{course}/discussions/{discussion}/reply` - Post reply

#### Admin Only

#### Course Management
- `GET /admin/courses` - List courses
- `POST /admin/courses` - Create course
- `PUT /admin/courses/{course}` - Update course
- `DELETE /admin/courses/{course}` - Delete course
- `GET /admin/courses/import/form` - Import form
- `POST /admin/courses/import` - Process import

#### Content Management
- `POST /admin/courses/{course}/content` - Create content
- `PUT /admin/courses/{course}/content/{content}` - Update content
- `DELETE /admin/courses/{course}/content/{content}` - Delete content

#### Quiz Management
- `POST /admin/courses/{course}/quiz` - Create quiz
- `POST /admin/courses/{course}/quiz/{quiz}/questions` - Add questions

## Models & Relationships

```
Course
  - belongsTo CategoryCategory
  - belongsTo Facilitator
  - hasMany CourseDates
  - hasMany CourseEnrollees
  - hasMany CourseContents
  - hasMany CourseQuizzes
  - hasMany CourseDiscussions
  - hasMany CourseLiveSessionshas
  - hasMany CoursePayments

CourseEnrollee
  - belongsTo User
  - belongsTo Course
  - belongsTo CourseDate
  - belongsTo CourseVenue
  - hasMany CourseContentCompletions
  - hasMany QuizSubmissions
  - hasMany CoursePayments
  - hasOne CourseCertificate

CourseContent
  - belongsTo Course
  - hasMany CourseContentCompletions

CourseQuiz
  - belongsTo Course
  - hasMany QuizQuestions
  - hasMany QuizSubmissions

CourseLiveSession
  - belongsTo Course
  - belongsTo Facilitator
  - hasMany LiveSessionAttendances
```

## Migration Instructions

1. **Upgrade PHP** to 8.2+ (current: 7.4.33)
2. **Run migrations:**
   ```bash
   php artisan migrate
   ```
3. **Seed initial data** (optional):
   ```bash
   php artisan db:seed --class=CourseSeeder
   ```

## File Structure

```
app/
  ├─ Http/Controllers/
  │   ├─ CourseController.php
  │   ├─ CourseCategoryController.php
  │   ├─ CourseEnrollmentController.php
  │   ├─ CourseContentController.php
  │   ├─ CourseQuizController.php
  │   ├─ CourseImportController.php
  │   ├─ FacilitatorController.php
  │   ├─ LiveSessionController.php
  │   └─ CourseDiscussionController.php
  ├─ Models/
  │   ├─ CourseCategory.php
  │   ├─ Facilitator.php
  │   ├─ Course.php
  │   ├─ CourseDate.php
  │   ├─ CourseVenue.php
  │   ├─ CourseEnrollee.php
  │   ├─ CourseContent.php
  │   ├─ CourseContentCompletion.php
  │   ├─ CourseQuiz.php
  │   ├─ QuizQuestion.php
  │   ├─ QuizAnswer.php
  │   ├─ QuizSubmission.php
  │   ├─ QuizSubmissionAnswer.php
  │   ├─ CourseDiscussion.php
  │   ├─ DiscussionReply.php
  │   ├─ CourseLiveSession.php
  │   ├─ LiveSessionAttendance.php
  │   ├─ CourseCarouselImage.php
  │   ├─ CoursePayment.php
  │   └─ CourseCertificate.php
  ├─ Services/
  │   └─ CourseCSVImportService.php
database/
  ├─ migrations/
  │   ├─ 2026_02_19_000001_create_course_categories_table.php
  │   ├─ 2026_02_19_000002_create_facilitators_table.php
  │   ├─ 2026_02_19_000003_create_courses_table.php
  │   ├─ ... (and more)
resources/
  ├─ views/
  │   ├─ courses/
  │   │   ├─ index.blade.php
  │   │   ├─ show.blade.php
  │   │   ├─ enrollment.blade.php
  │   │   ├─ my-enrollments.blade.php
  │   │   ├─ learn/
  │   │   │   ├─ content.blade.php
  │   │   │   ├─ quiz.blade.php
  │   │   │   └─ quiz-result.blade.php
  │   │   ├─ discussions/
  │   │   ├─ live-session.blade.php
  │   │   └─ upcoming-sessions.blade.php
  │   ├─ admin/
  │   │   ├─ courses/
  │   │   ├─ facilitators/
  │   │   ├─ course-categories/
  │   │   ├─ course-quizzes/
  │   │   ├─ course-contents/
  │   │   ├─ live-sessions/
  │   │   └─ course-enrollments/
routes/
  └─ web.php (updated with course routes)
```

## Testing Instructions (PENDING)

All features need comprehensive testing:
- Unit tests for each model
- Feature tests for enrollment flow
- Browser tests for UI interactions
- Integration tests for payment processing
- API endpoint tests

## Next Steps

1. **PHP Upgrade** - Upgrade to PHP 8.2+
2. **Database Migration** - Run migrations to create tables
3. **View Templates** - Create Blade templates for admin interfaces
4. **Payment Integration** - Integrate payment gateway (Stripe, Paystack, etc.)
5. **Email Notifications** - Setup enrollment and completion emails
6. **Testing** - Comprehensive test suite

## Security Considerations

- ✓ Role-based access control (admin, facilitator, student)
- ✓ Enrollment verification before content access
- ✓ Payment verification before course access
- ✓ SQL injection prevention (Eloquent ORM)
- ⚠️ CSRF protection (Laravel middleware)
- ⚠️ Rate limiting (recommended for payment endpoints)
- ⚠️ Content security policy (recommend implementing)

## Performance Optimizations

- Database indexes on frequently queried columns
- Eager loading relationships to prevent N+1 queries
- Caching for frequently accessed data
- Pagination for large result sets
- Image optimization for course covers

---

**Version:** 1.0.0  
**Last Updated:** February 19, 2026  
**Status:** Complete Schema & Controller Implementation
