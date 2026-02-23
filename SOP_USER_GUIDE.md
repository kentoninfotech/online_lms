# Standard Operating Procedures (SOP) & User Guide
## Online LMS Platform

**Version:** 1.0  
**Last Updated:** February 23, 2026  
**Document Owner:** System Administrator

---

## Table of Contents
1. [Introduction](#introduction)
2. [User Roles & Permissions](#user-roles--permissions)
3. [Admin Operations](#admin-operations)
4. [Instructor Operations](#instructor-operations)
5. [Student Operations](#student-operations)
6. [Common Workflows](#common-workflows)
7. [Troubleshooting](#troubleshooting)

---

## Introduction

The Online Learning Management System (LMS) is a comprehensive platform for delivering and managing educational content. This document provides step-by-step procedures for all user types to effectively use the system.

### System Access
- **Admin Portal:** `http://localhost:8000/admin/dashboard`
- **Instructor Portal:** `http://localhost:8000/instructor/dashboard`
- **Student Portal:** `http://localhost:8000/student/dashboard`
- **Public Homepage:** `http://localhost:8000`

---

## User Roles & Permissions

### Admin
- Full system access
- Create and manage courses
- Manage users and roles
- Configure site branding and design
- Access all administrative panels
- View analytics and reports

### Instructor
- Create and edit courses
- Manage course content
- Review student submissions
- View course analytics
- Manage quiz questions
- Conduct live sessions

### Student
- Enroll in courses
- Access course materials
- Submit assignments
- Take quizzes
- Participate in discussions
- View certificates

### Facilitator
- Support instructor functions
- Manage course dates and schedules
- Monitor attendance
- Create live session links

---

## Admin Operations

### 1. Site Configuration

#### Branding Management
**Path:** `/admin/site-builder/logos`

**Steps:**
1. Navigate to Admin Dashboard
2. Click "Site Builder" → "Logos & Branding"
3. Update the following fields:
   - **Site Name:** Your organization name
   - **Tagline:** Short motto or description
   - **Logo Height:** 20-200px (default: 50px)
   - **Logo Files:**
     - Upload light logo (for light backgrounds)
     - Upload dark logo (for dark backgrounds)
     - Upload favicon (64x64px recommended)
4. Toggle visibility:
   - ☑ Show Logo in Navbar
   - ☑ Show Site Name
   - ☑ Show Tagline
5. Click "Save Changes"

#### Design & Layout
**Path:** `/admin/site-builder/design`

**Steps:**
1. Go to Site Builder → Design & Layout
2. Configure colors:
   - Main background color
   - Navbar background (can use gradients)
   - Navbar text color
   - Container background
3. Upload background image (optional)
4. Set opacity level (0-100%)
5. Save changes

### 2. Course Management

#### Creating a Course
**Path:** `/admin/courses`

**Steps:**
1. Click "Add Course" button
2. Fill in course details:
   - Course title
   - Description
   - Category
   - Level (Local, International, Diploma)
   - Price (if applicable)
   - Featured image
3. Add course content (lessons, quizzes)
4. Set course dates and duration
5. Configure access permissions
6. Publish course

#### Managing Course Content
**Path:** `/admin/learning-content`

**Steps:**
1. Navigate to Learning Content
2. For each content item:
   - Add title and description
   - Select content type (text, video, file, etc.)
   - Upload media files
   - Set sequence/order
   - Choose visibility (published/draft)
3. Save content

#### Creating Quizzes
**Path:** `/admin/quizzes`

**Steps:**
1. Go to Course Quizzes
2. Click "Create Quiz"
3. Configure quiz settings:
   - Title and description
   - Number of questions
   - Time limit (in minutes)
   - Passing score percentage
   - Number of attempts allowed
4. Add questions:
   - Select question type
   - Add question text
   - Add answer options
   - Mark correct answer(s)
5. Shuffle options if needed
6. Publish quiz

### 3. User Management

#### Adding Users
**Path:** `/admin/users`

**Steps:**
1. Click "Add User"
2. Enter user information:
   - Full name
   - Email address
   - Password (auto-generated or custom)
3. Assign role:
   - Admin
   - Instructor
   - Student
   - Facilitator
4. Set status (active/inactive)
5. Save user

#### Managing Enrollments
**Path:** `/admin/enrollments`

**Steps:**
1. View all enrollments
2. Filter by course or student
3. Update enrollment status
4. View enrollment progress
5. Export enrollment data if needed

### 4. Homepage Management

#### Edit Homepage Sections
**Path:** `/admin/homepage-settings`

**Editable Sections:**
- **Hero Section:** Main banner with CTA
- **About Section:** Company/platform information
- **Features Section:** Key features listing
- **Featured Courses:** Highlight specific courses
- **Testimonials:** Student reviews
- **Stats Section:** Key metrics display
- **CTA Section:** Call-to-action buttons
- **Contact Section:** Contact information
- **Carousel:** Banner slideshow

**Steps to Edit:**
1. Go to Homepage Settings
2. Click "Edit" on desired section
3. Choose between:
   - **Initialize Defaults:** Load template content
   - **Manual Entry:** Add custom content
4. Select field types (text, textarea, image)
5. Fill in content
6. Save changes
7. View live preview on homepage

---

## Instructor Operations

### 1. Dashboard Overview
**Path:** `/instructor/dashboard`

**Key Metrics:**
- Total courses created
- Total students enrolled
- Pending assignments
- Upcoming live sessions

### 2. Course Management

#### Editing Course Content
**Steps:**
1. Go to "My Courses"
2. Select a course
3. Edit course settings (title, description, etc.)
4. Add/edit lessons
5. Upload course materials
6. Set lesson visibility
7. Publish changes

#### Managing Assignments
**Steps:**
1. Create assignment in course
2. Set due date
3. Define submission requirements
4. Review student submissions
5. Provide feedback and grades
6. Send notifications to students

### 3. Quiz Management

#### Creating Quiz Questions
**Steps:**
1. Go to Course → Quizzes
2. Click "Add Question"
3. Choose question type:
   - Multiple choice
   - True/False
   - Short answer
   - Essay
4. Enter question text
5. Add answer options
6. Mark correct answer(s)
7. Set point value
8. Save question

### 4. Live Sessions

#### Scheduling a Live Session
**Path:** `/instructor/live-sessions`

**Steps:**
1. Click "Schedule Session"
2. Enter session details:
   - Title
   - Date and time
   - Duration
   - Description
3. Select platform (Zoom/in-platform)
4. Generate meeting link
5. Add facilitators/co-hosts
6. Notify students
7. Publish session

#### Conducting Session
1. Open session at scheduled time
2. Share Zoom/meeting link with students
3. Record session (if enabled)
4. Record attendance
5. End session and save recording

---

## Student Operations

### 1. Course Enrollment

#### Finding and Enrolling in Courses
**Steps:**
1. Go to Courses page
2. Browse by category or level
3. Click on desired course
4. Review course details
5. Click "Enroll" button
6. Complete payment (if applicable)
7. Access course immediately

### 2. Course Access

#### Viewing Course Materials
**Steps:**
1. Go to "My Courses"
2. Select enrolled course
3. View course outline
4. Click lesson to view content
5. Mark lesson as complete
6. Access downloadable materials
7. Track progress

#### Submitting Assignments
**Steps:**
1. Navigate to assignment
2. Review requirements
3. Prepare submission
4. Click "Submit Assignment"
5. Upload files or enter text
6. Confirm submission
7. Receive confirmation message
8. Monitor grade/feedback

### 3. Taking Quizzes

#### Quiz Attempt
**Steps:**
1. Go to Quiz section
2. Click "Start Quiz"
3. Read instructions carefully
4. Answer all questions
5. Review answers before submission
6. Click "Submit Quiz"
7. View results and feedback
8. Review correct answers

### 4. Discussions

#### Participating in Course Discussions
**Steps:**
1. Go to Discussions section
2. View discussion threads
3. Click "Reply" on a topic
4. Write your response
5. Submit reply
6. View other student responses
7. Upvote helpful responses

### 5. Certificates

#### Earning and Downloading Certificates
**Steps:**
1. Complete all course requirements
2. Achieve passing score on final assessment
3. Certificate appears in "My Certificates"
4. Click "Download" to save PDF
5. Share on social media or add to resume

---

## Common Workflows

### Workflow 1: Create and Launch a Course

1. **Admin/Instructor Creates Course**
   - Navigate to Courses
   - Click "Add Course"
   - Fill in course details
   - Upload featured image
   - Save course (set to Draft)

2. **Add Course Content**
   - Go to Learning Content
   - Add lessons, videos, documents
   - Organize by sequence
   - Set as Draft

3. **Create Assessments**
   - Add quizzes with questions
   - Set quiz parameters
   - Publish quizzes

4. **Configure Access**
   - Set course price
   - Set visibility
   - Add course dates
   - Configure enrollment settings

5. **Publish Course**
   - Change status from Draft to Published
   - Course appears on homepage
   - Students can enroll

### Workflow 2: Student Learning Path

1. **Discovery**
   - Browse courses on homepage
   - Filter by category/level
   - Read course descriptions

2. **Enrollment**
   - Click Enroll
   - Complete payment if required
   - Receive enrollment confirmation

3. **Learning**
   - Access course materials
   - Complete lessons in order
   - Download resources

4. **Assessment**
   - Take quizzes
   - Submit assignments
   - Get feedback

5. **Completion**
   - Complete all requirements
   - Receive certificate
   - Leave review (optional)

### Workflow 3: Live Session Delivery

1. **Scheduling (Instructor)**
   - Create live session
   - Set date/time
   - Add description

2. **Preparation**
   - Test audio/video
   - Prepare materials
   - Send pre-session notification

3. **Delivery**
   - Start session
   - Share screen if needed
   - Monitor attendance
   - Record session

4. **Follow-up**
   - Upload recording
   - Share notes/materials
   - Answer follow-up questions

---

## Troubleshooting

### Common Issues & Solutions

#### Issue: User cannot log in
**Solution:**
1. Verify email address is correct
2. Request password reset
3. Check if account is active
4. Clear browser cache and try again
5. Contact admin if issue persists

#### Issue: Course content not displaying
**Solution:**
1. Verify content is published
2. Check visibility settings
3. Ensure content file is uploaded
4. Clear browser cache
5. Try different browser

#### Issue: Quiz not saving answers
**Solution:**
1. Check internet connection
2. Ensure browser allows cookies
3. Try refreshing page
4. Use different browser
5. Contact support

#### Issue: Payment processing fails
**Solution:**
1. Verify payment details
2. Check if card is authorized
3. Try different payment method
4. Contact payment support
5. Admin can manually enroll student

#### Issue: Live session link not working
**Solution:**
1. Verify meeting link is active
2. Check timezone settings
3. Ensure instructor started session
4. Try refreshing page
5. Contact instructor

#### Issue: Assignment feedback not visible
**Solution:**
1. Refresh page
2. Check email for notifications
3. Verify submission was successful
4. Check browser notifications
5. Contact instructor

### Contact & Support
- **Email:** support@lms.local
- **Help Center:** `/help`
- **Live Chat:** Available during business hours
- **Ticket System:** Submit support ticket in user menu

---

## Best Practices

### For Administrators
- Regularly backup database
- Monitor user activity
- Keep plugins/software updated
- Test new features before rollout
- Document custom configurations

### For Instructors
- Organize course content logically
- Provide clear learning objectives
- Use multimedia content
- Set realistic deadlines
- Respond to student inquiries promptly
- Record live sessions for asynchronous access

### For Students
- Complete lessons sequentially
- Participate in discussions
- Submit assignments before deadlines
- Take advantage of resources
- Ask questions and seek help
- Review quiz feedback

---

**Document Version:** 1.0  
**Last Updated:** February 23, 2026  
**Next Review Date:** June 23, 2026
