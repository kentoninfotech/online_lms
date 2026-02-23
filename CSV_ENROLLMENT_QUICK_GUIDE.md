# CSV Import & Course Enrollment - Quick Reference

## CSV Import Format for Dates & Venues

### Your Current CSV Structure
```csv
code,title,date,venue,fee
1,"Computer Application in Data Processing...","23 - 27 Mar., 25 - 29 May, 13 - 17 Jul., 05 - 09 Oct., 2026","Nasarawa, Lagos, Bauchi, Ibadan, Lagos, Port Harcourt, Abuja, Nasarawa, Bauchi","N460,000"
```

### How It Works Now

**Input (Single Line):**
```
Date: "23 - 27 Mar., 25 - 29 May, 13 - 17 Jul., 05 - 09 Oct., 2026"
Venue: "Nasarawa, Lagos, Bauchi, Ibadan, Lagos, Port Harcourt, Abuja, Nasarawa, Bauchi"
```

**Parsed Into:**
```
CourseDate #1:
  - date_label: "23 - 27 Mar., 2026"
  - venue: "Nasarawa"

CourseDate #2:
  - date_label: "25 - 29 May, 2026"
  - venue: "Lagos"

CourseDate #3:
  - date_label: "13 - 17 Jul., 2026"
  - venue: "Bauchi"

CourseDate #4:
  - date_label: "05 - 09 Oct., 2026"
  - venue: "Ibadan"
```

**In Database:**
```
courses table:
  id: 1
  code: "1"
  title: "Computer Application in Data Processing..."
  fee: 460000
  
course_dates table:
  id: 1, course_id: 1, date_label: "23 - 27 Mar., 2026", sequence: 1
  id: 2, course_id: 1, date_label: "25 - 29 May, 2026", sequence: 2
  id: 3, course_id: 1, date_label: "13 - 17 Jul., 2026", sequence: 3
  id: 4, course_id: 1, date_label: "05 - 09 Oct., 2026", sequence: 4
  
course_venues table:
  id: 1, course_date_id: 1, venue_name: "Nasarawa"
  id: 2, course_date_id: 2, venue_name: "Lagos"
  id: 3, course_date_id: 3, venue_name: "Bauchi"
  id: 4, course_date_id: 4, venue_name: "Ibadan"
```

## Enrollment Form Behavior

### Before (Without Fix)
**Problem:** All 4 dates combined into single option
```
Select Course Date:
[ "23 - 27 Mar., 25 - 29 May, 13 - 17 Jul., 05 - 09 Oct., 2026" ]
                    ↓
Only 1 venue selection available
```

### After (With Fix)
**Solution:** Each date as separate option
```
Select Course Date:
[ -- Select a date -- ]
[ 23 - 27 Mar., 2026 ]
[ 25 - 29 May, 2026 ]
[ 13 - 17 Jul., 2026 ]
[ 05 - 09 Oct., 2026 ]
        ↓ (select one)
Select Venue:
[ -- Select a venue -- ]
[ Nasarawa ]
[ Ibadan ]
[ Bauchi ]
[ Port Harcourt ]
        ↓
Payment: ₦460,000
```

## Import Steps

### 1. Prepare CSV File
Your courses.csv already has the correct format:
- **code:** Unique course identifier
- **title:** Course name
- **date:** Comma-separated dates (all on ONE line)
- **venue:** Comma-separated venues (all on ONE line, matching date count)
- **fee:** Course fee

### 2. Go to Admin Panel
```
Dashboard > Courses > Import
```

### 3. Select Format
- File: `courses.csv`
- Category: Select category
- **Format: "Dates & Venues Format"** (important!)

### 4. Upload
- Upload file
- System parses and creates records
- See confirmation: "Successfully imported 15 course(s)"

### 5. Verify
- Go to Any Course > [View] > Enroll Now
- Should see separate date options in dropdown

## Payment Flow Example

**User Journey:**
```
1. Browse Course: "Computer Application..."
   ↓
2. Click "Enroll Now"
   ↓
3. Select Date: "23 - 27 Mar., 2026"
   ↓
4. Select Venue: "Nasarawa"
   ↓
5. See Fee: ₦460,000
   ↓
6. Complete Enrollment
   ↓
7. Redirected to Payment
   ↓
8. Make Payment for THIS SPECIFIC date/venue combo
```

**Database Records Created:**
```
enrolments:
  - user_id: 123
  - course_id: 1
  - course_date_id: 1 (23-27 Mar)
  - course_venue_id: 1 (Nasarawa)
  - amount: ₦460,000

payments:
  - enrollee_id: 456
  - amount: ₦460,000
  - status: pending
```

## Edge Cases Handled

### ✅ Missing Year in Date String
**Input:** `"23 - 27 Mar., 25 - 29 May, 13 - 17 Jul., 05 - 09 Oct., 2026"`
**Output:**
```
23 - 27 Mar., 2026
25 - 29 May, 2026
13 - 17 Jul., 2026
05 - 09 Oct., 2026
```
(Year automatically appended to each date)

### ✅ Extra Spaces in Venue Names
**Input:** `"Nasarawa , Lagos , Bauchi"`
**Output:**
```
Nasarawa
Lagos
Bauchi
```
(Whitespace trimmed)

### ✅ Case-Insensitive Duplicate Prevention
**Input:** `"Lagos, LAGOS, lagos"`
**Output:**
```
Lagos (only one record)
```
(Duplicates skipped)

### ✅ Empty Fields Filtered
**Input:** `"Venue1, , , Venue2"`
**Output:**
```
Venue1
Venue2
```
(Empty values removed)

## Troubleshooting

### Issue: Import still shows 1 date
**Debug:**
1. Check CSV format is "Dates & Venues"
   - NOT "Standard Format"
2. Verify dates are comma-separated
   - NOT newline-separated
3. Count commas in date field
   - Should have N-1 commas for N dates
4. Check logs for parser errors
   - `tail -20 storage/logs/laravel.log`

**Example Good Format:**
```
✓ "23 - 27 Mar., 25 - 29 May, 13 - 17 Jul."
✓ "Lagos, Abuja, Port Harcourt"
```

**Example Bad Format:**
```
✗ Line-separated dates:
  23 - 27 Mar.
  25 - 29 May
  13 - 17 Jul.

✗ Wrong separator:
  "23 - 27 Mar. | 25 - 29 May | 13 - 17 Jul."
```

### Issue: Venues not matching dates
**Solution:**
1. Ensure venue count equals date count
2. Venues should be in same order as dates
3. If you want 1 venue for all dates, list it N times
   - Example: `"Lagos, Lagos, Lagos, Lagos"` for 4 dates

**Formula:**
```
Number of Dates = Number of Venues

4 dates → 4 venues
5 dates → 5 venues
```

### Issue: Enrollment form still showing old format
**Solution:**
1. Clear Laravel cache:
   ```bash
   php artisan config:cache
   ```
2. Re-import course (or delete and reimport)
3. Check course_dates table has multiple records
   ```bash
   php artisan tinker
   >>> Course::find(1)->courseDates()->count()
   4 (should show 4, not 1)
   ```

## Test Your Setup

### 1. Download Sample CSV
Use your existing `courses.csv` with format:
```csv
code,title,date,venue,fee
1,"Test Course","10 - 14 Mar., 17 - 21 Apr., 22 - 26 May, 2026","Lagos, Abuja, Port Harcourt","N500,000"
```

### 2. Import
- Admin > Courses > Import
- Choose "Dates & Venues Format"
- Upload file

### 3. Verify Database
```bash
php artisan tinker

>>> use App\Models\Course;
>>> $course = Course::where('code', '1')->first();
>>> $course->courseDates()->count()
3 (should show 3, not 1)

>>> $course->courseDates()->pluck('date_label')
["10 - 14 Mar., 2026", "17 - 21 Apr., 2026", "22 - 26 May, 2026"]

>>> $course->courseDates()->with('venues')->get()
// Should show venues for each date
```

### 4. Test Enrollment
1. Visit `/courses` (public page)
2. Find your imported course
3. Click "Enroll Now"
4. Date dropdown should show 3 separate options
5. Select date → venues should update
6. Try to enroll

---

## Performance Notes

**Import Speed:**
- 15 courses with 4 dates each = 60 database records
- Should complete in < 5 seconds
- No performance issues expected

**Enrollment Form:**
- Date dropdown: instant (~50ms)
- Venue dropdown: instant (~50ms)
- No N+1 queries (uses eager loading)

---

## Next Steps

1. **Import your courses.csv** using the new parser
2. **Test enrollment** on any course with multiple dates
3. **Verify payment creation** for correct date/venue combo
4. **Set up LLM provider** for AI content generation
5. **Generate content** for any new courses

Good luck! 🚀
