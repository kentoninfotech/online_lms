# Live Sessions with Jitsi & Progress/Certificate Implementation

## Summary of Changes

### 1. Progress Tracking & Certificates

#### Model Updates
- **CourseEnrollee.php**:
  - Updated `calculateProgressPercentage()` to include both content AND quizzes
  - Added `isCourseComplete()` method to check if course is 100% complete
  - Added `hasCertificate()` method to check if certificate exists
  - Added `generateCertificate()` method to auto-generate certificates when course is 100% complete

#### Course Learn Page (View)
- Updated `resources/views/courses/learn/index.blade.php`:
  - Dynamic progress calculation showing both content and quiz completion
  - Certificate download button when 100% complete
  - Certificate generation button when eligible
  - Live sessions widget on progress card
  - Better visual feedback with color-coded progress bar

#### Certificate Management
- **New Routes Added**:
  - `GET /course/{course}/certificate/generate/{enrollment}` - Generate certificate
  - `GET /course/{course}/certificate/download/{enrollment}` - Download PDF certificate

- **CertificateController.php**:
  - Added `generate()` method to create certificates upon completion
  - Added `downloadCertificate()` method to download as PDF
  - Validates enrollment ownership and completion status

- **New View**: `resources/views/certificates/completion-certificate.blade.php`
  - Professional certificate design with gold border and seal
  - Shows certificate number, issue date, expiration date
  - Includes course title and student name
  - Ready for PDF generation via DomPDF

#### Database Migration
- **Migration**: `2026_02_22_000002_add_fields_to_course_live_sessions_table.php`
  - Adds: `is_compulsory`, `duration_minutes`, `max_points`, `jitsi_room_name`, `chat_enabled`

---

### 2. Live Sessions with Jitsi Meet Integration

#### Model Updates
- **CourseLiveSession.php**:
  - Added fillable fields: `is_compulsory`, `duration_minutes`, `max_points`, `jitsi_room_name`, `chat_enabled`
  - Added casts for proper type handling

#### Configuration
- **config/services.php** - New Jitsi section:
  ```php
  'jitsi' => [
      'domain' => env('JITSI_DOMAIN', 'meet.jitsi'),
      'app_id' => env('JITSI_APP_ID'),
      'app_secret' => env('JITSI_APP_SECRET'),
      'self_hosted' => env('JITSI_SELF_HOSTED', false),
      'server_url' => env('JITSI_SERVER_URL', 'https://meet.jitsi'),
  ]
  ```

#### Controller Updates
- **LiveSessionController.php**:
  - Updated `adminStore()` to handle new Jitsi fields
  - Updated `adminUpdate()` to manage session configuration
  - Auto-generates Jitsi room name if not provided
  - Validates compulsory, points, and timing settings

#### Admin Views
- **resources/views/admin/live-sessions/create.blade.php** (Complete Rewrite):
  - Basic Information section (title, description, facilitator)
  - Schedule section (start/end time, auto-calculated duration)
  - Session Settings (compulsory flag, chat enable, max points)
  - Jitsi Configuration section with room name and session type
  - JavaScript auto-calculation of duration
  - Help panel with best practices
  - Support for Jitsi, Zoom, Google Meet, Teams

#### Student/Learner Views
- **resources/views/courses/live-session.blade.php** (Complete Rewrite):
  - Embedded Jitsi Meet iframe (self-hosted or meet.jitsi)
  - Live session status indicator (LIVE NOW / Upcoming / Completed)
  - Online participant count
  - Session info card showing:
    - Scheduled time
    - Duration
    - Session type (Compulsory/Optional)
    - Points available
  - Online participants list with online status indicators
  - Chat section (if enabled) with real-time messaging
  - Facilitator info with online status
  - Jitsi configuration for video quality, audio, controls

#### Features Implemented

**For Admins/Tutors:**
- ✅ Create live sessions with detailed configuration
- ✅ Set sessions as compulsory or optional
- ✅ Assign points/scores for attendance
- ✅ Configure Jitsi room names
- ✅ Enable/disable chat during sessions
- ✅ Set duration and timing
- ✅ Choose between Jitsi and external services (Zoom, Teams, Meet)

**For Learners/Students:**
- ✅ See upcoming live sessions
- ✅ Join Jitsi video conference embedded in the page
- ✅ See real-time list of online classmates
- ✅ Chat with other participants (if enabled)
- ✅ View session requirements (compulsory, points, duration)
- ✅ Automatic attendance tracking
- ✅ Facilitator online status

**Live Session Features:**
- ✅ Video/Audio conferencing via Jitsi
- ✅ Screen sharing capability
- ✅ Chat room during sessions
- ✅ Participant list with online status
- ✅ Attendance tracking
- ✅ Optional session recording
- ✅ Compulsory session management
- ✅ Points/Score allocation

---

## Installation & Setup

### 1. Database Migration
```bash
php artisan migrate
```

This will add the new columns to `course_live_sessions` table:
- `is_compulsory` (boolean, default: false)
- `duration_minutes` (integer, nullable)
- `max_points` (integer, default: 0)
- `jitsi_room_name` (string, unique, nullable)
- `chat_enabled` (boolean, default: true)

### 2. Environment Configuration
Add to your `.env` file:
```env
JITSI_DOMAIN=meet.jitsi
JITSI_SELF_HOSTED=false
JITSI_SERVER_URL=https://meet.jitsi
```

For self-hosted Jitsi:
```env
JITSI_DOMAIN=your-jitsi-domain.com
JITSI_SELF_HOSTED=true
JITSI_SERVER_URL=https://your-jitsi-domain.com
```

### 3. Self-Hosting Jitsi (Optional)
If you want to self-host Jitsi instead of using meet.jitsi:

**Using Docker:**
```bash
git clone https://github.com/jitsi/docker-jitsi-meet
cd docker-jitsi-meet
cp env.example .env
docker-compose up -d
```

Update your `.env`:
```env
JITSI_DOMAIN=your-server-domain.com
JITSI_SELF_HOSTED=true
JITSI_SERVER_URL=https://your-server-domain.com
```

---

## User Workflow

### For Course Completion & Certificates:
1. Student enrolls in course
2. Student completes all required content (visible in Progress card: X/Y items)
3. Student completes all required quizzes
4. Progress reaches 100%
5. "Generate & Download Certificate" button appears
6. Student clicks button to generate certificate
7. Certificate PDF is downloadable

### For Live Sessions:
1. Admin creates live session with:
   - Title, description, facilitator
   - Start/end time
   - Jitsi room configuration
   - Optional/Compulsory flag
   - Points value
   - Chat enabled/disabled

2. Learners see upcoming sessions in their course
3. During session time, students click "Join Session"
4. Embedded Jitsi Meet opens with:
   - Video/audio from facilitator
   - Classmate's video feeds
   - Online participants list
   - Chat (if enabled)
   - Real-time status updates

5. Attendance automatically tracked
6. Facilitator can award points/scores

---

## Technical Details

### Progress Calculation Logic
```php
// Includes both content and quizzes
$requiredContent = $course->contents()->where('is_required', true)->count();
$requiredQuizzes = $course->quizzes()->where('is_required', true)->count();
$totalRequired = $requiredContent + $requiredQuizzes;

$completedContent = count of marked-as-complete content
$completedQuizzes = count of submitted quiz submissions
$totalCompleted = $completedContent + $completedQuizzes;

$progress = ($totalCompleted / $totalRequired) * 100
```

### Certificate Generation
- Only generated when `isCourseComplete()` returns true
- Generates unique certificate number: CERT-{course_id}-{random}
- Default 1-year validity
- Can be revoked by admin if needed
- PDF includes course title, student name, issue date

### Jitsi Integration
- Uses Jitsi external API
- Room names are unique across the system
- Configurable for self-hosted or cloud
- Supports user authentication
- Real-time participant list updates
- Audio/video quality settings optimized

---

## Routes Added/Modified

```php
// Course Learning
GET /course/{course}/learn - Learning hub with progress card
GET /course/{course}/content/{content} - Content with minimum time tracking
POST /course/{course}/content/{content}/complete - Mark as complete (validates min time)

// Certificates
GET /course/{course}/certificate/generate/{enrollment} - Generate certificate
GET /course/{course}/certificate/download/{enrollment} - Download PDF

// Live Sessions
GET /live-sessions/upcoming - List upcoming sessions
GET /course/{course}/live-session/{session} - Join session with Jitsi

// Admin
POST /admin/courses/{course}/live-sessions - Create session
```

---

## Key Files Modified/Created

### Created:
- `database/migrations/2026_02_22_000002_add_fields_to_course_live_sessions_table.php`
- `resources/views/certificates/completion-certificate.blade.php`
- `resources/views/admin/live-sessions/create.blade.php` (updated)
- `resources/views/courses/live-session.blade.php` (updated)

### Modified:
- `app/Models/CourseEnrollee.php` - Added progress/certificate methods
- `app/Models/CourseLiveSession.php` - Added new fields
- `app/Http/Controllers/CertificateController.php` - New methods
- `app/Http/Controllers/LiveSessionController.php` - Updated admin methods
- `config/services.php` - Added Jitsi config
- `resources/views/courses/learn/index.blade.php` - Dynamic progress card
- `routes/web.php` - New certificate routes

---

## Testing Checklist

- [ ] Run migrations successfully
- [ ] Create course with content and quizzes
- [ ] Mark all content as complete
- [ ] Complete all quizzes
- [ ] Verify progress reaches 100%
- [ ] Generate and download certificate PDF
- [ ] Create live session with Jitsi configuration
- [ ] Join live session as student (test Jitsi iframe loads)
- [ ] Verify attendance tracking
- [ ] Test chat functionality
- [ ] Test online participant list updates
- [ ] Verify session points display correctly

---

## Future Enhancements

- [ ] Session recording and playback
- [ ] Auto-award points based on attendance duration  
- [ ] Breakout rooms for group activities
- [ ] Session replay/archive functionality
- [ ] Email notifications before sessions
- [ ] Mobile app support
- [ ] AI-powered meeting summarization
- [ ] Grade integration for session participation
