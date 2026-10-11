# Testing DateTime Format Implementation

## Login Credentials
- **URL**: https://nountutor.com.ng/login
- **Username**: lmsadmin@nountutor.com.ng
- **Password**: @@GLMSAdmin22

## Test Scenarios

### Test 1: Create a New Lesson (Admin)
**URL**: https://nountutor.com.ng/admin/courses/[COURSE_ID]/lessons/create

**Steps**:
1. Navigate to Admin Dashboard
2. Select a course
3. Go to "Create New Lesson"
4. Fill in the form with:
   - Topic: "Test Lesson - DD/MM/YYYY Format"
   - Description: "Testing the new date format"
   - Start Time: **16/10/2026 15:00** (Format: DD/MM/YYYY HH:mm)
   - End Time: **16/10/2026 16:00** (Format: DD/MM/YYYY HH:mm)
   - Instructor: Select any instructor
   - Capacity: 30
5. Click "Create Lesson"

**Expected Results**:
- ✅ Form accepts the DD/MM/YYYY HH:mm format
- ✅ No validation errors
- ✅ Lesson is created successfully
- ✅ Lesson details display with correct datetime format

---

### Test 2: Edit an Existing Lesson
**URL**: https://nountutor.com.ng/admin/courses/[COURSE_ID]/lessons/[LESSON_ID]/edit

**Steps**:
1. Navigate to a lesson
2. Click "Edit"
3. Verify that existing datetime shows in DD/MM/YYYY HH:mm format
4. Modify the start time to: **16/10/2026 14:30**
5. Modify the end time to: **16/10/2026 15:30**
6. Click "Save Changes"

**Expected Results**:
- ✅ Existing datetime displays in DD/MM/YYYY HH:mm format
- ✅ Form accepts the updated DD/MM/YYYY HH:mm format
- ✅ Changes are saved successfully
- ✅ Duration auto-calculates correctly (should be 60 minutes)

---

### Test 3: Create a Live Session
**URL**: https://nountutor.com.ng/admin/courses/[COURSE_ID]/live-sessions/create

**Steps**:
1. Navigate to Admin Dashboard
2. Select a course
3. Click "Schedule Live Session"
4. Fill in the form with:
   - Session Title: "Test Live Session - DD/MM Format"
   - Description: "Testing datetime picker"
   - Facilitator: Select any facilitator
   - Start Date & Time: **16/10/2026 15:00**
   - End Date & Time: **16/10/2026 16:00**
   - Session Type: Jitsi Meet
5. Click "Schedule Session"

**Expected Results**:
- ✅ Form accepts DD/MM/YYYY HH:mm format
- ✅ Placeholder shows "DD/MM/YYYY HH:mm"
- ✅ Duration auto-calculates (60 minutes)
- ✅ Session is created successfully

---

### Test 4: Edit a Live Session
**URL**: https://nountutor.com.ng/admin/courses/[COURSE_ID]/live-sessions/[SESSION_ID]/edit

**Steps**:
1. Navigate to a live session
2. Click "Edit"
3. Verify existing datetime is in DD/MM/YYYY HH:mm format
4. Change start time to: **16/10/2026 14:00**
5. Change end time to: **16/10/2026 15:00**
6. Click "Save Changes"

**Expected Results**:
- ✅ Existing datetime displays in DD/MM/YYYY HH:mm format
- ✅ Form accepts modifications in DD/MM/YYYY HH:mm format
- ✅ Duration auto-calculates correctly
- ✅ Changes persist

---

### Test 5: View Calendar with Lessons
**URL**: https://nountutor.com.ng/admin/courses/[COURSE_ID]/lessons/calendar

**Steps**:
1. Navigate to course calendar view
2. Observe all lesson times displayed

**Expected Results**:
- ✅ All lesson times display in DD/MM/YYYY HH:mm format (or similar readable format)
- ✅ Dates are properly formatted with "/" separator

---

### Test 6: Publish Calendar & Lessons
**Steps**:
1. Create new lesson(s) with DD/MM/YYYY HH:mm format
2. Mark as "Published"
3. Navigate to student dashboard
4. Verify published lessons display correctly

**Expected Results**:
- ✅ Published lessons show correctly formatted datetimes
- ✅ No formatting errors or display issues
- ✅ Calendar events display with correct times

---

## Format Validation Rules

### What WORKS ✅
- `16/10/2026 15:00` - Correct format
- `01/01/2027 08:30` - Correct format
- `31/12/2025 23:59` - Correct format

### What FAILS ❌
- `16-10-2026 15:00` - Wrong format (hyphens instead of slashes)
- `2026-10-16 15:00` - Wrong format (ISO/backend format)
- `16/10/26 15:00` - Invalid year format
- `16/10/2026 25:00` - Invalid hour (must be 00-23)
- `16/10/2026 15:60` - Invalid minute (must be 00-59)

---

## JavaScript Features Implemented

### Auto-Formatting
When user enters datetime, JavaScript will:
1. Accept typed input in DD/MM/YYYY HH:mm format
2. Auto-format on blur if input matches pattern
3. Validate day (01-31), month (01-12), hour (00-23), minute (00-59)

### Duration Calculation
When start and end times are entered:
1. Parse both datetimes
2. Calculate difference in minutes
3. Auto-fill "Duration (minutes)" field

### Format Conversion
On form submission:
1. Ensure input is in DD/MM/YYYY HH:mm format
2. Send to backend as DD/MM/YYYY HH:mm
3. Backend converts to Carbon datetime object
4. Database stores as UTC datetime

---

## Backend Processing

### Validation
- Rule: `date_format:d/m/Y H:i`
- Accepts: DD/MM/YYYY HH:mm
- Rejects: Any other format

### Conversion
```php
$datetime = Carbon::createFromFormat('d/m/Y H:i', $input);
// Carbon object is stored in database as UTC
```

### Display
```php
// When displaying in forms:
$lesson->start_time->format('d/m/Y H:i') // Returns: 16/10/2026 15:00
```

---

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Form shows old datetime format | Clear browser cache or hard refresh (Ctrl+Shift+R) |
| Date not accepted | Verify format is exactly DD/MM/YYYY HH:mm |
| Duration not calculating | Ensure both start and end times are filled |
| Validation error "must match format" | Change format from - to / and Y-m-d to d/m/Y |
| Times showing in browser timezone | Times are UTC-stored, displayed in user's timezone as entered |

---

## Success Indicators ✅

After completing all tests, you should see:
- [x] All datetime inputs accept DD/MM/YYYY HH:mm format
- [x] No validation errors for correct format
- [x] Existing lessons display datetime in DD/MM/YYYY HH:mm format
- [x] Duration auto-calculates when both times are entered
- [x] Lessons can be created and edited with new format
- [x] Published lessons display correctly formatted times
- [x] Calendar shows events with proper datetime formatting
- [x] Live sessions accept and display DD/MM/YYYY HH:mm format

---

## Files to Verify (for developers)

```
✅ /app/Http/Controllers/CourseLessonController.php
   - Line ~65: store() method - date_format:d/m/Y H:i
   - Line ~125: update() method - date_format:d/m/Y H:i

✅ /app/Http/Controllers/LiveSessionController.php
   - Line ~90: adminStore() method - date_format:d/m/Y H:i
   - Line ~127: adminUpdate() method - date_format:d/m/Y H:i

✅ /resources/views/admin/course-lessons/create.blade.php
   - Line ~75-85: start_time and end_time inputs with placeholder

✅ /resources/views/admin/course-lessons/edit.blade.php
   - Line ~75-85: start_time and end_time inputs displaying d/m/Y H:i format

✅ /resources/views/admin/live-sessions/create.blade.php
   - Line ~75-85: scheduled_start and scheduled_end inputs

✅ /resources/views/admin/live-sessions/edit.blade.php
   - Line ~75-85: scheduled_start and scheduled_end inputs

✅ /resources/views/dashboard/add-lesson.blade.php
   - Line ~130: start_time input with DD/MM/YYYY HH:mm format

✅ /resources/views/dashboard/edit-lesson.blade.php
   - Line ~130: start_time input with timezone-aware format display
```

---

## Notes

- All times are stored in UTC in the database
- Display format converts to DD/MM/YYYY HH:mm for user convenience
- Timezone awareness is handled by Laravel's Carbon library
- JavaScript date parsing handles multiple input formats for backward compatibility
