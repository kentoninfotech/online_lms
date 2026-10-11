# Course Lessons Calendar - Quick Start Guide

## 🚀 Getting Started in 5 Minutes

### Step 1: Access the Feature
Navigate to any course and look for the "Calendar" or "Lessons" button. Or go directly to:
```
/courses/{courseId}/calendar
/courses/{courseId}/lessons
```

### Step 2: Create Your First Lesson
1. Click **"Add Lesson"** button
2. Fill in required fields:
   - **Topic**: "Introduction to Physics"
   - **Start Time**: "Oct 15, 2026 10:00 AM"
   - **End Time**: "Oct 15, 2026 11:30 AM"
   - **Instructor**: Select from dropdown
3. Optional: Add location, meeting link, capacity, description
4. Check **"Publish"** to make visible to students
5. Click **"Create Lesson"**

### Step 3: View on Calendar
- Switch to **Calendar View** to see your lesson
- Click on a lesson event to see details
- Check right sidebar for upcoming lessons

---

## 📅 Calendar Views

### Month View (Default)
- See all lessons for the month
- Click events for details
- Navigate with Previous/Next buttons

### Week View
- Detailed hourly schedule
- Better for busy schedules
- Shows 7 days at a time

### Day View
- Hour-by-hour breakdown
- Perfect for single-day focus
- Shows all time slots

---

## 📋 Lesson Details You Can Set

| Field | Required? | Example | Notes |
|-------|-----------|---------|-------|
| **Topic** | ✅ Yes | "Chapter 5: Thermodynamics" | Lesson title |
| **Start Time** | ✅ Yes | "Oct 20, 2026 2:00 PM" | When lesson begins |
| **End Time** | ✅ Yes | "Oct 20, 2026 3:30 PM" | When lesson ends |
| **Instructor** | ✅ Yes | "Dr. Sarah Johnson" | Who's teaching |
| **Description** | ❌ No | "We'll cover heat transfer..." | What to learn |
| **Location** | ❌ No | "Science Lab, Room 212" | Physical location |
| **Meeting Link** | ❌ No | "https://zoom.us/j/..." | For online lessons |
| **Capacity** | ❌ No | "30" | Max students |
| **Notes** | ❌ No | "Bring lab coats" | Internal notes |

---

## 🎯 Quick Actions

### ✏️ Edit a Lesson
1. Find lesson in list or calendar
2. Click pencil icon / Edit button
3. Make changes
4. Save

### 🗑️ Delete a Lesson
1. Click trash icon
2. Confirm deletion
3. Done!

### 👁️ View Details
1. Click lesson in calendar or list
2. See full details, attendees, meeting info
3. Click to join meeting link if available

---

## 🔗 Meeting Links (Online Lessons)

### Common Platforms
- **Zoom**: `https://zoom.us/j/123456789`
- **Google Meet**: `https://meet.google.com/abc-defg-hij`
- **Microsoft Teams**: `https://teams.microsoft.com/l/meetup-join/...`
- **Custom Platform**: Any HTTP/HTTPS URL

### How to Use
1. Create meeting in your platform (Zoom, Teams, etc.)
2. Copy the meeting URL
3. Paste in **"Meeting Link"** field
4. Students click link from calendar to join

---

## 📊 Lesson Status Colors

| Status | Meaning | Color |
|--------|---------|-------|
| 📘 **Upcoming** | Lesson is in the future | Blue |
| 🟡 **Ongoing** | Lesson is happening now | Yellow |
| ✅ **Completed** | Lesson already happened | Green |
| ⚫ **Draft** | Not published yet | Gray |

---

## 👥 Managing Students

### Automatic Enrollment
- When students enroll in course, they automatically see published lessons

### Manual Attendance Tracking
- View attendees for each lesson (in lesson details page)
- Mark attendance as: Registered, Attended, Absent, Cancelled

---

## 🔒 Who Can Do What?

| Action | Admin | Author | Instructor | Student |
|--------|-------|--------|-----------|---------|
| Create Lesson | ✅ | ✅ | ❌ | ❌ |
| Edit Lesson | ✅ | ✅ | ❌ | ❌ |
| Delete Lesson | ✅ | ✅ | ❌ | ❌ |
| View Calendar | ✅ | ✅ | ✅ | ✅ |
| View Lessons | ✅ | ✅ | ✅ | ✅ |

---

## 💡 Pro Tips

✅ **DO:**
- Set both start and end times for duration calculation
- Publish lessons when they're final and ready
- Use real locations for in-person lessons
- Include Zoom links for online sessions
- Set capacity for limited-enrollment lessons

❌ **DON'T:**
- Leave times blank (they won't show on calendar)
- Delete lessons students might depend on
- Publish incomplete/test lessons
- Use broken or invalid URLs for meeting links

---

## 🆘 Common Issues & Fixes

### "Lesson doesn't show on calendar"
- ✅ Check if lesson is **published**
- ✅ Verify **start_time** and **end_time** are set
- ✅ Make sure you're viewing the correct month/year

### "Meeting link doesn't work"
- ✅ Check URL starts with `http://` or `https://`
- ✅ Test the link in a new tab
- ✅ Make sure meeting is active/created

### "Students can't see the lesson"
- ✅ Confirm lesson is **published**
- ✅ Check student is **enrolled** in course
- ✅ Verify course is **active** and visible

---

## 📱 Responsive Design

The calendar works on:
- ✅ Desktop browsers (Chrome, Firefox, Safari)
- ✅ Tablets (iPad, Android)
- ✅ Mobile phones (responsive layout)

---

## 🔄 Auto-Refresh

The **Upcoming Lessons** sidebar:
- Refreshes automatically every 5 minutes
- Shows real-time updates
- No manual refresh needed

---

## 📲 Keyboard Shortcuts (Calendar)

- ⬅️ **Previous** - Navigate to previous month/week
- ➡️ **Next** - Navigate to next month/week
- **T** - Jump to today
- **M** - Month view
- **W** - Week view  
- **D** - Day view

---

## 🎓 Example Scenarios

### Scenario 1: Online Physics Class
```
Topic: "Quantum Mechanics - Uncertainty Principle"
Date: Nov 5, 2026
Time: 2:00 PM - 3:30 PM
Instructor: Dr. Anderson
Meeting Link: https://zoom.us/j/98765432
```

### Scenario 2: In-Person Chemistry Lab
```
Topic: "Titration Experiment"
Date: Nov 7, 2026
Time: 1:00 PM - 3:00 PM
Instructor: Prof. Chen
Location: Chemistry Lab 301
Capacity: 25 (per lab equipment)
```

### Scenario 3: Hybrid Biology Lecture
```
Topic: "Cell Biology - Mitochondria"
Date: Nov 10, 2026
Time: 10:00 AM - 11:00 AM
Instructor: Dr. Martinez
Location: Auditorium A (In-person)
Meeting Link: https://meet.google.com/abc-defg-hij (Online option)
```

---

## 📞 Need Help?

See the full documentation: `COURSE_LESSONS_CALENDAR_FEATURE.md`

Or contact your administrator for:
- Permission issues
- Database problems
- API integration questions

---

**Version**: 1.0  
**Last Updated**: October 11, 2026  
**Features**: Create, View, Edit, Delete, Calendar, Upcoming List  
**Views**: List, Calendar (Month/Week/Day), Sidebar  
