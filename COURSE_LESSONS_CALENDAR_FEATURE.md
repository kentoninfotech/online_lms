# Course Lessons Calendar Feature - Documentation

## Overview

The Course Lessons Calendar is a comprehensive lesson scheduling and management system that allows Admin, Course Authors, and Instructors to:

1. **Create and Schedule Lessons** - Set lesson topics, dates, and times
2. **Manage Lesson Details** - Add descriptions, locations, meeting links, and capacity
3. **Visual Calendar View** - Interactive calendar displaying all scheduled lessons
4. **Upcoming Lessons Sidebar** - Quick view of upcoming lessons with important details
5. **Track Attendance** - Monitor student enrollment and attendance

---

## Feature Components

### 1. Models

#### CourseLesson
- **Table**: `course_lessons`
- **Key Fields**:
  - `course_id` - Associated course
  - `instructor_id` - Lesson instructor
  - `topic` - Lesson topic/title
  - `description` - Detailed description
  - `scheduled_date` - Date of lesson
  - `start_time` - Start datetime
  - `end_time` - End datetime
  - `location` - Physical location (classroom, room number, etc.)
  - `meeting_link` - Online meeting URL (Zoom, Google Meet, etc.)
  - `is_published` - Visibility status
  - `capacity` - Maximum student capacity

- **Computed Attributes**:
  - `formatted_date_time` - Returns formatted date and time string
  - `status` - Returns lesson status (upcoming, ongoing, completed, draft)
  - `duration_minutes` - Calculates lesson duration

#### CourseLessonAttendee
- **Table**: `course_lesson_attendees`
- **Purpose**: Tracks which students attended each lesson
- **Key Fields**:
  - `course_lesson_id` - Reference to lesson
  - `course_enrollee_id` - Reference to enrolled student
  - `attended_at` - When student attended
  - `status` - Attendance status (registered, attended, absent, cancelled)
  - `feedback` - Optional student/instructor feedback

### 2. Controller: CourseLessonController

#### Methods Available

| Method | Route | Purpose |
|--------|-------|---------|
| `calendar` | GET `/courses/{course}/calendar` | Display calendar view |
| `index` | GET `/courses/{course}/lessons` | List all lessons |
| `create` | GET `/courses/{course}/lessons/create` | Show create form |
| `store` | POST `/courses/{course}/lessons` | Save new lesson |
| `show` | GET `/courses/{course}/lessons/{lesson}` | View lesson details |
| `edit` | GET `/courses/{course}/lessons/{lesson}/edit` | Show edit form |
| `update` | PUT `/courses/{course}/lessons/{lesson}` | Update lesson |
| `destroy` | DELETE `/courses/{course}/lessons/{lesson}` | Delete lesson |
| `getCalendarLessons` | GET `/api/courses/{course}/lessons/calendar` | API endpoint for calendar events |
| `getUpcomingLessons` | GET `/api/upcoming-lessons` | API endpoint for upcoming lessons |

---

## How to Use

### Accessing the Feature

1. **From Course Management**: 
   - Go to a course detail/edit page
   - Look for "Calendar" or "Lessons" button in the course header
   - Click to navigate to lesson management

2. **Direct URL**:
   - Calendar view: `/courses/{courseId}/calendar`
   - Lesson list: `/courses/{courseId}/lessons`

### Creating a Lesson

1. Navigate to the course's lesson management area
2. Click "Add Lesson" button
3. Fill in the required fields:
   - **Topic** (required) - Lesson title/subject
   - **Start Time** (required) - When lesson begins
   - **End Time** (required) - When lesson ends
   - **Instructor** (required) - Who is teaching
4. Optionally fill:
   - **Description** - Detailed lesson information
   - **Scheduled Date** - Date reference
   - **Location** - Physical classroom location
   - **Meeting Link** - URL for online lesson (Zoom, Teams, etc.)
   - **Capacity** - Max student attendance
   - **Notes** - Internal notes
5. Check "Publish" to make visible to students
6. Click "Create Lesson"

### Viewing Lessons

#### List View
- Table showing all lessons with:
  - Topic and location
  - Instructor name
  - Date & Time
  - Duration
  - Status badge
  - Publication status
  - Action buttons (View, Edit, Delete)

#### Calendar View
- **Interactive FullCalendar** with:
  - Month, Week, and Day views
  - Previous/Next navigation
  - Today button
  - Clickable events showing lesson details
  
- **Right Sidebar** showing:
  - Upcoming lessons list
  - Color-coded by status (upcoming, ongoing, completed)
  - Quick access to:
    - Event date and time
    - Location/Meeting link
    - Instructor name
    - Direct navigation to full details

### Editing a Lesson

1. From list or calendar view, click the lesson
2. Click "Edit" button
3. Modify any fields
4. Click "Save Changes"

### Deleting a Lesson

1. From list view, click the delete button (trash icon)
2. Confirm deletion
3. Lesson is permanently removed

---

## Calendar Features

### Interactive Calendar

The calendar uses **FullCalendar** v6.1.10 and supports:

#### View Modes
- **Month View** - Overview of all lessons in a month
- **Week View** - Detailed view of lessons in a specific week
- **Day View** - Hour-by-hour schedule for a day

#### Navigation
- **Previous/Next Buttons** - Move between months/weeks/days
- **Today Button** - Jump to today's date
- **Title** - Shows current month/week/day being viewed

#### Event Interactions
- **Click Event** - Shows popup with lesson details:
  - Topic
  - Instructor
  - Date & Time
  - Location
  - Status
  - Meeting link
  - Description

### Upcoming Lessons Sidebar

The right sidebar automatically displays:

- **10 Most Upcoming Lessons** - Listed chronologically
- **Color-Coded Status**:
  - Blue border = Upcoming (future lesson)
  - Yellow border = Ongoing (currently happening)
  - Green border = Completed (past lesson)
  
- **Quick Information**:
  - Lesson topic
  - Course name
  - Date and time with icons
  - Location (if available)
  - Meeting link (clickable to join)
  - Instructor name

- **Auto-Refresh**: Updates every 5 minutes automatically

---

## Lesson Status

Lessons automatically get a status based on their timing:

| Status | Condition | Color |
|--------|-----------|-------|
| **Draft** | No start time set | Gray |
| **Upcoming** | Start time is in the future | Blue |
| **Ongoing** | Current time is between start and end | Yellow |
| **Completed** | End time has passed | Green |

---

## API Endpoints

### Get Calendar Events

**URL**: `GET /api/courses/{courseId}/lessons/calendar?month=10&year=2026`

**Response**:
```json
[
  {
    "id": 1,
    "title": "Introduction to Physics",
    "start": "2026-10-15T10:00:00",
    "end": "2026-10-15T11:30:00",
    "description": "Basic physics concepts",
    "location": "Room 101",
    "instructor": "Dr. Smith",
    "status": "upcoming",
    "meetingLink": "https://zoom.us/j/123456"
  }
]
```

### Get Upcoming Lessons

**URL**: `GET /api/upcoming-lessons?courseId={courseId}`

**Response**:
```json
[
  {
    "id": 1,
    "topic": "Introduction to Physics",
    "courseId": 1,
    "courseName": "Physics 101",
    "instructor": "Dr. Smith",
    "startTime": "2026-10-15T10:00:00",
    "endTime": "2026-10-15T11:30:00",
    "location": "Room 101",
    "meetingLink": "https://zoom.us/j/123456",
    "formattedDateTime": "Oct 15, 2026 | 10:00 AM - 11:30 AM"
  }
]
```

---

## Database Schema

### course_lessons table
```sql
- id (bigint, primary key)
- course_id (bigint, foreign key → courses)
- instructor_id (bigint, foreign key → instructors)
- topic (varchar 255, required)
- description (text, nullable)
- scheduled_date (date, nullable)
- start_time (datetime, nullable)
- end_time (datetime, nullable)
- location (varchar 255, nullable)
- meeting_link (varchar 255, nullable)
- is_published (boolean, default false)
- capacity (int, nullable)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

Indexes:
- course_id
- instructor_id
- start_time
- is_published
```

### course_lesson_attendees table
```sql
- id (bigint, primary key)
- course_lesson_id (bigint, foreign key → course_lessons)
- course_enrollee_id (bigint, foreign key → course_enrollees)
- attended_at (datetime, nullable)
- status (enum: registered|attended|absent|cancelled)
- feedback (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

Unique Index:
- lesson_enrollee_unique (course_lesson_id, course_enrollee_id)

Other Indexes:
- course_lesson_id
- status
```

---

## Authorization & Permissions

- **Create/Edit/Delete Lessons**: Requires `update` permission on Course (Admin or Course Author)
- **View Lessons**: Requires `view` permission on Course (any authenticated user)
- **Manage Attendees**: Requires `update` permission on Course

---

## Best Practices

1. **Set Both Times**: Always set both start and end times for accurate duration calculation
2. **Publish When Ready**: Only publish lessons when they're ready for students to see
3. **Use Meeting Links**: Always include Zoom/Teams links for online lessons
4. **Add Descriptions**: Provide detailed descriptions to help students understand content
5. **Set Capacity**: If you have a cap on students, set the capacity field
6. **Use Locations**: Specify physical locations for in-person lessons

---

## Common Tasks

### Create a Zoom Lesson
1. Go to course lessons
2. Click "Add Lesson"
3. Enter topic and times
4. Paste your Zoom meeting URL in "Meeting Link"
5. Publish
6. Share the calendar URL with students

### Reschedule a Lesson
1. Click "Edit" on the lesson
2. Change the start and end times
3. Save
4. Students will see the new time in their calendar

### Set Capacity Limit
1. Edit the lesson
2. Enter number in "Capacity" field
3. This shows maximum students who can attend
4. Useful for practical sessions with equipment limits

### Add Instructor Notes
1. Edit the lesson
2. Add details in "Notes" field
3. These are for internal use only, not shown to students

---

## Troubleshooting

### Lessons Not Appearing on Calendar
- Check if lesson is **published** (checkbox on create/edit form)
- Verify **start_time** and **end_time** are set
- Check the calendar is viewing the correct month/year

### Meeting Link Not Showing
- Ensure it's a valid URL starting with `http://` or `https://`
- If link has special characters, they must be properly encoded

### Students Not Seeing Lessons
- Confirm lesson has **is_published = true**
- Check student is enrolled in the course
- Verify course is active and visible to students

### Attendee List Empty
- Attendees are only created when:
  - Student enrolls in course AND lesson is created
  - Or attendee records are manually created for tracking
- Current system shows enrolled students by default

---

## Future Enhancements

Potential improvements:
- Email notifications for upcoming lessons
- Student RSVP/attendance confirmation
- Lesson recording storage and replay
- Quiz/assessment integration
- Lesson templates for recurring topics
- Bulk lesson creation from CSV
- Calendar export (iCal format)
- Integration with Google Calendar/Outlook
- Automated attendance marking based on login
- Lesson feedback and ratings

---

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review authorization and permissions
3. Check database migrations have run successfully
4. Review browser console for JavaScript errors
5. Check Laravel logs in `storage/logs/`

