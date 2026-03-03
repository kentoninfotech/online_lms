# Quick Start: Course Display Settings

## Access Point
**Admin URL**: `/admin/homepage-settings/course-display`

**Navigation Path**: 
1. Go to Admin Dashboard
2. Click "Homepage Settings" (left sidebar)
3. Click the green "Course Display" card

## What Was Implemented

### ✅ 4 Display Modes for Courses
1. **Default** - Only featured courses
2. **Categories Dropdown** - Featured courses + category filter
3. **Level Tabs** - Featured courses + Local/International/Diploma tabs  
4. **Both** - Featured courses + category filter + level tabs

### ✅ Customizable Settings
- Show/hide featured courses section
- Choose 3, 4, or 5 columns per row
- Set max courses to display
- Select which categories appear in dropdown
- Select which levels appear as tabs
- Toggle "All Courses" and "All Programs" options

### ✅ Database Structure
All settings stored in `homepage_settings` table:
```
section: 'course_display'
Keys:
- show_featured_courses (boolean)
- course_display_mode (string)
- courses_per_row (integer)
- max_courses_display (integer)
- show_all_categories_option (boolean)
- selected_categories (JSON array)
- show_all_levels_option (boolean)
- selected_levels (JSON array)
```

## Files Modified
1. ✅ Created: `/resources/views/admin/homepage-settings/course-display-settings.blade.php`
2. ✅ Updated: `/app/Http/Controllers/Admin/HomepageSettingController.php` (added 2 methods)
3. ✅ Updated: `/app/Http/Controllers/CourseController.php` (added settings to index)
4. ✅ Updated: `/routes/web.php` (added 2 new routes)
5. ✅ Updated: `/resources/views/admin/homepage-settings/index.blade.php` (added link)
6. ✅ Updated: `/resources/views/courses/index.blade.php` (added 2 new sections + JS)

## New Routes
- `GET /admin/homepage-settings/course-display` → Show settings form
- `PUT /admin/homepage-settings/course-display` → Save settings

## How Course Display Modes Work On Frontend

### Mode: Default
- Only featured courses visible
- No additional filters

### Mode: Categories Dropdown
```
Featured Courses section
↓
Browse By Category section
├─ Dropdown filter
└─ All courses list (filterable)
```

### Mode: Level Tabs
```
Featured Courses section
↓
Browse By Program Type section
├─ Tab 1: All Programs (optional)
├─ Tab 2: Local courses
├─ Tab 3: International courses
└─ Tab 4: Diploma courses
```

### Mode: Both
```
Featured Courses section
↓
Browse By Category section (with filter)
↓
Browse By Program Type section (with tabs)
```

## Setting Defaults
If no settings exist, these defaults apply:
- Mode: `default`
- Columns: `3`
- Max courses: `12`
- Show all categories: `true`
- Show all levels: `true`
- Selected levels: `Local, International, Diploma`

## JavaScript Features
- **Client-side filtering**: Category dropdown filters instantly
- **No page reload**: Smooth user experience
- **Responsive**: Works on all devices

## Admin Form Features
- **Live preview**: Shows what mode users will see
- **Checkboxes**: Easy category and level selection
- **Validation**: Form validates on submit
- **Success message**: Confirms when settings saved

## Testing Checklist
- [ ] Admin can access `/admin/homepage-settings`
- [ ] "Course Display" card appears (green)
- [ ] Can open course display settings page
- [ ] Can select different display modes
- [ ] Can select categories and levels
- [ ] Settings save successfully
- [ ] Homepage shows correct sections based on mode
- [ ] Category filter works (no page reload)
- [ ] Level tabs work correctly
- [ ] Mobile responsive CSS works

## Common Use Cases

### Use Case 1: Simple Course Catalog
- Mode: Default
- Just show featured courses

### Use Case 2: Category-Based Browsing
- Mode: Categories Dropdown
- Select all active categories
- Include "All Courses" option
- Users filter by interest

### Use Case 3: Multi-Level Programs
- Mode: Level Tabs
- Enable Local, International, Diploma
- Include "All Programs" option
- Users browse by program type

### Use Case 4: Full Experience
- Mode: Both
- Maximum flexibility
- Select all categories and levels
- Best for large catalogs

## Troubleshooting

**Settings not appearing?**
- Clear browser cache
- Hard refresh (Ctrl+F5)
- Check database for entries in `homepage_settings` table

**Routes not found?**
- Run `php artisan route:cache` 
- Then `php artisan route:clear`

**Styles not loading?**
- Run `npm run build` (if using Vite)
- Ensure Bootstrap is loaded in layout

## Future Enhancements Ideas
- Add course sorting (popularity, price, rating)
- Add search within display sections
- Add course recommendations
- Add saved favorites per user
- Add analytics dashboard
- Add course comparison tool
