# Course Import - Data Model Fix

## Problem
The original import approach was creating duplicate courses for each venue/date combination, causing:
- Multiple identical course records
- "Out of range" database errors for fees
- Inefficient data structure

## Solution Implemented
Changed from **flat structure** to **hierarchical relationship model**:

### Before (Incorrect)
```
Courses Table
├── Course Code: 1, Title: XYZ, Fee: 6500
├── Course Code: 1, Title: XYZ, Fee: 4500  (DUPLICATE)
├── Course Code: 1, Title: XYZ, Fee: 4500  (DUPLICATE)
└── Course Code: 1, Title: XYZ, Fee: 5500  (DUPLICATE)
```

### After (Correct)
```
Courses Table
└── Course Code: 1, Title: XYZ [saved ONCE]
    ↓
    Course Dates Table
    ├── Date 1: "13 - 17 Apr., 2026"
    │   ├── Venue: USA, Fee: 6,500 NGN
    │   └── Venues count: 1
    ├── Date 2: "25 - 29 May, 2026"
    │   ├── Venue: UAE Dubai, Fee: 4,500 NGN
    │   └── Venues count: 1
    ├── Date 3: "13 - 17 Jul., 2026"
    │   ├── Venue: South Africa, Fee: 4,500 NGN
    │   └── Venues count: 1
    └── Date 4: "07 - 11 Dec., 2026"
        ├── Venue: India, Fee: 5,500 NGN
        └── Venues count: 1
```

## Changes Made

### 1. Database Schema
- **Added** `fee` column to `course_venues` table via migration:
  - File: `database/migrations/2026_08_22_add_fee_to_course_venues_table.php`
  - Column: `decimal(10,2)` for storing venue-specific fees

### 2. Models Updated
- **CourseVenue Model**: Added `fee` to fillable and casts arrays
  - Now supports storing individual venue fees

### 3. Import Command
- **Created**: `app/Console/Commands/ImportCoursesFromCsv.php`
- **Features**:
  - Reads CSV with columns: CODE, COURSE TITLE, DATE, VENUE, FEE, CURRENCY
  - Creates course only once (checks for existing code)
  - Creates course_date for each date_label
  - Creates course_venue for each venue with its specific fee
  - Parses date labels (e.g., "13 - 17 Apr., 2026")
  - Extracts country from venue name

### 4. Data Import Results
```
Courses Processed: 8
  - Code 1: Sustainable Living Environmental Management
  - Code 2: Servicing And Maintenance of Portable Fire Extinguishers
  - Code 3: The Certified Fire Protection Specialist (CFPS)
  - ... (and 5 more)

Total Dates Created: 32
Total Venues Created: 32
Duplicate Courses: 0 ✓
```

## Data Structure

### Course Hierarchy
Each course now has:
1. **One course record** (no duplicates)
2. **Multiple dates** (one for each date label in CSV)
3. **Multiple venues per date** (one for each venue with its own fee)

### Database Tables Involved
- `courses` - Main course table (unchanged except for usage)
- `course_dates` - Dates linked to courses
- `course_venues` - Venues linked to dates (NEW: fee column)

## Example Query
To get all dates and venues for a course:
```php
$course = Course::with(['courseDates.venues'])->find($id);

foreach ($course->courseDates as $date) {
    echo $date->date_label; // "13 - 17 Apr., 2026"
    foreach ($date->venues as $venue) {
        echo $venue->venue_name; // "USA"
        echo $venue->fee; // 6500.00
    }
}
```

## Files Modified/Created

### New Files
- ✓ `database/migrations/2026_08_22_add_fee_to_course_venues_table.php`
- ✓ `app/Console/Commands/ImportCoursesFromCsv.php`
- ✓ `parse_courses.py` (data transformation)
- ✓ `verify_import.php` (verification script)
- ✓ `verify_import_detailed.php` (detailed verification)

### Updated Files
- ✓ `app/Models/CourseVenue.php` (added 'fee' to fillable and casts)

## Usage

### Run Import
```bash
php artisan import:courses "path/to/csv/file"
```

### Verify Data
```bash
php verify_import_detailed.php
```

## Import Status
✓ **COMPLETE** - All 8 courses imported successfully with proper hierarchical structure
✓ **NO DUPLICATES** - Each course saved only once
✓ **FEES PRESERVED** - Each venue has its own fee stored correctly
✓ **READY FOR USE** - Database properly normalized and optimized

---
**Date**: 2026-08-22
**Status**: ✓ Production Ready
