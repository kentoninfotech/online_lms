# Course Import Guide

## Overview
The course import feature allows you to bulk import courses into the LMS using CSV or Excel files. Two import formats are supported:

1. **Standard Format** - For basic course information
2. **With Dates & Venues** - For courses with multiple scheduling dates and different venues

---

## Standard Format

### Required Columns
- `code` - Unique course code (required)
- `title` - Course title (required)

### Optional Columns
- `subtitle` - Course subtitle
- `description` - Course description
- `facilitator_id` - ID of the course facilitator
- `fee` - Course fee amount
- `currency` - Currency code (NGN, USD, GBP, EUR) - defaults to NGN
- `course_hours` - Total course hours
- `is_online` - Is online course (1/true/yes or 0/false/no)
- `is_offline` - Is offline course (1/true/yes or 0/false/no)
- `is_featured` - Feature this course (1/true/yes or 0/false/no)
- `is_active` - Activate course (1/true/yes or 0/false/no)
- `max_enrollees` - Maximum number of students

### Example (Standard Format)
```csv
code,title,subtitle,description,facilitator_id,fee,currency,course_hours,is_online,is_offline,is_featured,is_active
CRS001,Web Development,Beginner to Advanced,Learn modern web development,1,50000,NGN,40,0,1,1,1
CRS002,Database Design,SQL & NoSQL,Master database design patterns,2,45000,NGN,30,1,1,0,1
```

### Notes
- Column order doesn't matter
- Include only the columns you need
- First row must contain headers
- Both `code` and `title` are required
- Course codes must be unique (already imported courses cannot be re-imported)

---

## With Dates & Venues Format

### Required Columns
- `code` - Unique course code
- `title` - Course title
- `date` - Course execution dates (multiline, line-break separated)
- `venue` - Course locations/venues (multiline, line-break separated)
- `fee` - Course fee

### Format Details

**DATE Field:**
- Enter multiple dates separated by **line breaks** (press Enter between each date)
- Each line represents one course date/session
- Example:
  ```
  Jan 15, 2025
  Feb 20, 2025
  Mar 25, 2025
  ```

**VENUE Field:**
- Enter venues matching each date, separated by **line breaks**
- On each line, multiple venues can be listed comma-separated
- Must have the same number of lines as the DATE field
- Example (matching 3 dates above):
  ```
  Lagos Tech Hub
  Abuja Office, Virtual
  Port Harcourt Center
  ```

### Example (Dates & Venues Format)
```csv
code,title,date,venue,fee
CRS001,Web Development,"Jan 15, 2025
Feb 20, 2025
Mar 25, 2025","Lagos Tech Hub
Abuja Office
Port Harcourt Center",50000
CRS002,Database Design,"Feb 10, 2025
Mar 15, 2025","Lekki Campus
Virtual",45000
```

### Duplicate Venue Handling
- If a venue name appears multiple times within the same course, only the **first occurrence** is recorded
- Subsequent duplicate venues are automatically ignored
- Venue comparison is case-insensitive (e.g., "Lagos Office" and "LAGOS OFFICE" are treated as the same)

### Notes
- Line breaks in CSV cells: Use actual line breaks (Enter key), not `\n` or `<br>`
- In Excel: Hold Alt+Enter to insert line breaks
- Each date must have a corresponding venue entry on the same line number
- Dates and venues create instances of CourseDate and CourseVenue records

---

## Common Errors & Solutions

### "Missing required columns"
**Error Message:** `Missing required columns: CODE, TITLE...`

**Cause:** Column headers don't match expected names

**Solution:**
- Check header row spelling (case-insensitive)
- Ensure columns are named exactly: CODE, TITLE, DATE, VENUE, FEE
- Remove extra spaces in column headers
- Verify file format is selected correctly (Standard vs. Dates & Venues)

### "Course code already exists"
**Error Message:** `Row X: Course code 'CRS001' already exists in database`

**Cause:** You're trying to import a course with a code that's already in the database

**Solution:**
- Use unique course codes for new courses
- Or delete existing course first before re-importing
- Or use a different code (e.g., CRS001-V2)

### "No valid dates found"
**Error Message:** `Row X: No valid dates found`

**Cause:** The DATE field is empty or has no valid entries

**Solution:**
- Ensure DATE field has at least one date entry
- Check that dates are on separate lines, not comma-separated
- Remove any empty lines in the DATE field

### "No courses imported. Check file format."
**Cause:** File reading failed or all rows had errors

**Solution:**
1. Check file format (CSV vs Excel)
2. Verify file encoding (UTF-8 recommended)
3. Ensure first row contains headers
4. Check file permissions (should be readable)
5. Look for detailed error messages in the alert box
6. Verify column names match exactly

---

## Creating Your CSV File

### Using Excel
1. Open a blank spreadsheet
2. Enter headers in first row
3. Enter data in subsequent rows
4. For multiline cells (Dates & Venues format):
   - Double-click the cell
   - Enter first value
   - Hold Alt+Enter to create new line
   - Enter next value
   - Repeat as needed
   - Press Enter to confirm
5. Save As → CSV UTF-8 (.csv)

### Using Google Sheets
1. Create a new spreadsheet
2. Enter headers and data
3. For multiline cells:
   - Double-click cell
   - Enter value
   - Press Ctrl+Enter to create new line
   - Enter next value
   - Close cell when done
4. Download → CSV format

### Using Text Editor
1. Open Notepad/VS Code
2. Create content with proper CSV structure
3. For multiline fields in CSV, use proper CSV escaping:
   ```csv
   code,title,date,venue,fee
   CRS001,Web Dev,"Jan 15, 2025
   Feb 20, 2025","Lagos Office
   Abuja Office",50000
   ```
4. Save with .csv extension
5. Ensure UTF-8 encoding

---

## Validation Rules

### All Formats
- Course code must be unique
- Course title is required and cannot be empty
- Fee should be a number (zeros, decimals allowed)
- Currency must be valid: NGN, USD, GBP, EUR (defaults to NGN)

### Dates & Venues Format
- Date field cannot be empty
- Must have same number of date and venue entries
- Venue names cannot be empty within date entries
- Duplicate venue names per course are automatically filtered

---

## Tips for Success

1. **Prepare Data First**
   - Make sure all course codes are unique
   - Validate all required fields are filled
   - Check for typos in facilitator IDs

2. **Test with Sample Data**
   - Import 1-2 courses first
   - Check results
   - Then do bulk import

3. **Use Consistent Format**
   - Date format: "Jan 15, 2025" or "2025-01-15"
   - Fee format: Numbers only (50000, not "50,000")
   - Currency: Valid codes only

4. **Check Error Messages**
   - Each error shows exact row number
   - Shows which field has the issue
   - Use this to fix your CSV file

5. **Backup First**
   - Export existing courses before bulk import
   - In case you need to rollback

---

## Support

If you encounter issues:
1. Check the error messages displayed after import attempt
2. Refer to the specific error solution above
3. Verify your file format matches the examples
4. Check column headers are spelled correctly
5. Ensure required fields are not empty

For persistent issues, contact your system administrator with:
- The exact error message
- Your CSV file content (sanitized)
- Which import format you're using
