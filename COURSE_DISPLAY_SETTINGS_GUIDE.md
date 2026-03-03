# Course Display Settings Feature Implementation

## Overview
A new admin feature has been added to the Homepage Settings that allows administrators to control how courses are displayed on the homepage with optional category dropdown and course level tabs (Local, International, Diploma).

## Features Added

### 1. **Admin Course Display Settings Page**
   - Location: `/admin/homepage-settings/course-display`
   - Access via: Homepage Settings → "Course Display" card

### 2. **Display Modes Available**
   - **Default View**: Only show featured courses (no additional options)
   - **With Category Dropdown**: Add a dropdown to filter courses by category
   - **With Course Level Tabs**: Display courses organized by type (Local, International, Diploma)
   - **With Both Options**: Show both category dropdown and level tabs

### 3. **Configuration Options**

#### Featured Courses Display
- Toggle to show/hide featured courses section
- Dropdown display mode selection

#### Courses Per Row
- Choose between 3, 4, or 5 columns for desktop view
- Defaults to 3 columns

#### Maximum Courses to Display
- Set limit on number of courses displayed (default: 12)
- Leave empty to show all active courses

#### Category Display Settings
- Select which categories appear in the dropdown filter
- Option to include "All Courses" option
- Only selected categories will be shown

#### Course Level Display Settings (Local, International, Diploma)
- Select which course levels appear as tabs
- Option to include "All Programs" tab
- Defaults to all three levels enabled

## Files Modified/Created

### New Files
1. **Admin View**: `/resources/views/admin/homepage-settings/course-display-settings.blade.php`
   - Admin interface for configuring course display options
   - Interactive preview showing selected display mode

### Modified Files

1. **Controller**: `/app/Http/Controllers/Admin/HomepageSettingController.php`
   - Added `showCourseDisplaySettings()` method
   - Added `updateCourseDisplaySettings()` method
   - Handles all course display setting operations

2. **Routes**: `/routes/web.php`
   - Added GET route: `/admin/homepage-settings/course-display`
   - Added PUT route: `/admin/homepage-settings/course-display` (update)

3. **Controller**: `/app/Http/Controllers/CourseController.php`
   - Updated `index()` method to fetch and pass course display settings
   - Settings passed as `$courseDisplaySettings` variable to view

4. **View**: `/resources/views/courses/index.blade.php`
   - Added "Browse By Category" section (conditional)
   - Added "Browse By Program Type" section with level tabs (conditional)
   - Added JavaScript for category filter functionality
   - Sections dynamically display based on admin settings

5. **Admin Index**: `/resources/views/admin/homepage-settings/index.blade.php`
   - Added "Course Display" settings card
   - Links to the new course display settings page

## How to Use

### For Administrators

1. Navigate to: **Admin Dashboard → Homepage Settings → Course Display**

2. Configure your preferred display mode:
   - Choose between Default, Category Dropdown, Level Tabs, or Both
   - Select courses per row layout
   - Set maximum courses to display

3. If using **Category Dropdown**:
   - Select which categories should appear in the dropdown
   - Choose if to include "All Courses" option

4. If using **Level Tabs**:
   - Select which course levels (Local, International, Diploma) to display
   - Choose if to include "All Programs" tab

5. Click **Save Settings** to apply changes

### For Users (on Homepage)

Users will see:
- **If Default Mode**: Only featured courses section
- **If Category Dropdown**: Featured courses + category filter dropdown
- **If Level Tabs**: Featured courses + tabs for course levels
- **If Both**: Featured courses + category dropdown + level tabs

## Database Settings Structure

Settings are stored in `homepage_settings` table under `section: 'course_display'` with keys:
- `show_featured_courses`: Boolean (0 or 1)
- `course_display_mode`: String (default, categories_dropdown, level_tabs, both)
- `courses_per_row`: Integer (3, 4, or 5)
- `max_courses_display`: Integer (limit or empty)
- `show_all_categories_option`: Boolean
- `selected_categories`: JSON array of category IDs
- `show_all_levels_option`: Boolean
- `selected_levels`: JSON array of level strings

## Example Usage

### Scenario 1: Show All Courses by Category
1. Set display mode: "With Category Dropdown"
2. Include "All Courses" option: ✓
3. Courses per row: 3
4. Select all or specific categories
5. Users see featured courses + category filter

### Scenario 2: Organize by Program Type
1. Set display mode: "With Course Level Tabs"
2. Include "All Programs" tab: ✓
3. Select: Local, International, Diploma
4. Users see featured courses + tabs for each level

### Scenario 3: Complete Course Browser
1. Set display mode: "With Both Options"
2. Select all categories and levels
3. Set courses per row: 4
4. Users get full browsing experience with both filters

## Responsive Design

- Category dropdown and level tabs are fully responsive
- Works seamlessly on mobile, tablet, and desktop
- Course cards adjust layout based on selected "courses per row" setting

## Next Steps (Optional Enhancements)

1. Add sorting options (popularity, price, rating)
2. Add course search functionality within filtered results
3. Add favorite/bookmark course feature
4. Add course recommendations based on browsing history
5. Create analytics dashboard to track most viewed courses/categories

## Support

For questions or issues, refer to the admin settings page which includes:
- Live preview of selected display mode
- Helpful tips and instructions
- Clear section references

## Technical Notes

- Settings are cached efficiently using Laravel's `HomepageSetting::getSetting()` method
- Uses JSON encoding for storing array data in database
- JavaScript filter is client-side for instant filtering without page reload
- All settings apply in real-time once saved
