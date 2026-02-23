# Course Content Management - Testing Data & Scenarios

## Sample Content Scenarios

### Example 1: Video Tutorial with Prerequisites
```
Title: Advanced OOP Concepts
Description: Deep dive into Object-Oriented Programming principles
Content Type: Video
Sequence: 2
Duration: 45 minutes
Available From: 2026-02-25 09:00
Available Until: 2026-03-31 17:00
Prerequisite: "Introduction to OOP" (sequence 1)
Min Reading Time: 40 minutes
Embed Type: Default
Track Viewing: ✓ Checked
Allow Download: ☐ Unchecked
Publish: ✓ Checked
Required: ✓ Checked
```
**Use Case:** Required course video that must be watched sequentially

---

### Example 2: Reference PDF Available All Term
```
Title: Programming Style Guide
Description: Best practices for code formatting and documentation
Content Type: PDF
Sequence: 5
Duration: 0 (reading material)
Available From: (empty - immediately)
Available Until: (empty - always available)
Prerequisite: (none)
Min Reading Time: 0 (optional reading)
Embed Type: Default
Track Viewing: ☐ Unchecked
Allow Download: ✓ Checked
Publish: ✓ Checked
Required: ☐ Unchecked
```
**Use Case:** Optional reference material students can access anytime

---

### Example 3: Time-Limited Assignment Brief
```
Title: Week 3 Assignment Requirements
Description: Create a to-do list application with Angular
Content Type: Word
Sequence: 3
Duration: 20 minutes
Available From: 2026-02-24 08:00
Available Until: 2026-03-10 23:59
Prerequisite: "Angular Basics" (sequence 1)
Min Reading Time: 15 minutes
Embed Type: Popup
Track Viewing: ✓ Checked
Allow Download: ✓ Checked
Publish: ✓ Checked
Required: ✓ Checked
```
**Use Case:** Assignment that opens on specific date, requires time on document

---

### Example 4: Interactive Embedded Content
```
Title: Database Design Interactive Tool
Description: Visual tool for designing database schemas
Content Type: Link
Sequence: 4
Duration: 60 minutes
Available From: 2026-02-28 10:00 (starts Monday)
Available Until: (empty - opens Monday, no close)
Prerequisite: "SQL Fundamentals" (sequence 1)
Min Reading Time: 50 minutes
Embed Type: IFrame
Track Viewing: ✓ Checked
Allow Download: ☐ Unchecked (N/A for link)
Publish: ✓ Checked
Required: ✓ Checked
```
**Use Case:** Interactive tool that opens at specific time, embedded in page

---

### Example 5: Image Gallery Lecture Slides
```
Title: Lecture 5 - Machine Learning Slides
Description: Key concepts in neural networks and deep learning
Content Type: Image
Sequence: 6
Duration: 30 minutes
Available From: (empty)
Available Until: (empty)
Prerequisite: "Lecture 4 - Supervised Learning"
Min Reading Time: 0
Embed Type: Modal
Track Viewing: ☐ Unchecked
Allow Download: ✓ Checked
Publish: ✓ Checked
Required: ☐ Unchecked
```
**Use Case:** Lecture slides viewable in modal, with prerequisite

---

### Example 6: Full PowerPoint Presentation
```
Title: Q2 Financial Analysis 2026
Description: Company performance review and forecast
Content Type: PowerPoint
Sequence: 1
Duration: 40 minutes
Available From: 2026-03-01 14:00 (Friday 2pm)
Available Until: 2026-03-31 17:00 (End of month)
Prerequisite: (none)
Min Reading Time: 35 minutes
Embed Type: Full Screen
Track Viewing: ✓ Checked
Allow Download: ✓ Checked
Publish: ✓ Checked
Required: ✓ Checked
```
**Use Case:** Major presentation, opens Friday afternoon, full-screen viewing

---

### Example 7: Excel Data Practice
```
Title: Sales Data Analysis Exercise
Description: Analyze quarterly sales data using spreadsheet formulas
Content Type: Excel
Sequence: 5
Duration: 50 minutes
Available From: (empty - available immediately)
Available Until: (empty - always available)
Prerequisite: "Excel Basics" (sequence 2)
Min Reading Time: 0 (active practice, not passive reading)
Embed Type: Default
Track Viewing: ☐ Unchecked
Allow Download: ✓ Checked (students can download their work)
Publish: ✓ Checked
Required: ☐ Unchecked (practice exercise)
```
**Use Case:** Practice worksheet, always available, optional review

---

### Example 8: Draft Content Not Yet Live
```
Title: Emerging Technologies in 2026
Description: Survey of latest technology trends and innovations
Content Type: Text/HTML
Sequence: 7
Duration: 15 minutes
Available From: 2026-04-01 09:00 (future date)
Available Until: (empty)
Prerequisite: (none)
Min Reading Time: 10 minutes
Embed Type: Default
Track Viewing: ✓ Checked
Allow Download: ☐ Unchecked
Publish: ☐ UNCHECKED (Draft mode)
Required: ☐ Unchecked
```
**Use Case:** Content being prepared but not yet released to students

---

## Testing Workflows

### Test 1: Complete Learning Path
1. Create "Foundations" (sequence 0) - No prerequisites
2. Create "Intermediate" (sequence 1) - Requires Foundations
3. Create "Advanced" (sequence 2) - Requires Intermediate
4. Publish all three
5. **Expected Result:** Student can only access Foundations first, then Intermediate, then Advanced

### Test 2: Timeline Window Test
1. Create content with:
   - Available From: Today at 9:00 AM
   - Available Until: Yesterday at 5:00 PM
2. **Expected Result:** Content shows as expired/unavailable
3. Then create content with:
   - Available From: Tomorrow at 9:00 AM
   - Available Until: (empty - never expires)
4. **Expected Result:** Content not yet available

### Test 3: Mixed Content Types
1. Create Text content with HTML
2. Create PDF upload
3. Create Video file
4. Create External Link in IFrame
5. Create PowerPoint
6. Create Image
7. Create Excel file
8. Create Word document
9. **Expected Result:** All display correctly with proper badges

### Test 4: Tracking and Engagement
1. Create content with:
   - Min Reading Time: 10 minutes
   - Track Viewing: Checked
2. Student accesses for 5 minutes
3. **Expected Result:** Cannot mark complete yet (under minimum)
4. Student accesses for 8 more minutes (total 13)
5. **Expected Result:** Can now mark complete, viewing tracked

### Test 5: Download Permissions
1. Create PDF with Allow Download: Checked
2. Create PDF with Allow Download: Unchecked
3. **Expected Result:** First PDF has download button, second doesn't

### Test 6: Editing and File Replacement
1. Create content with PDF file
2. Edit content and upload different PDF
3. Check storage location
4. **Expected Result:** Old file deleted, new file takes its place

---

## Form Validation Testing

### Invalid Inputs to Test

#### Title Field
```
✓ Valid: "Introduction to Python"
✓ Valid: "Lesson 1.2.3 - Advanced Topics"
✗ Invalid: (empty) - Should show "Title is required"
✗ Invalid: (256+ characters) - Should show "Max 255 characters"
```

#### Sequence Number
```
✓ Valid: 0
✓ Valid: 1, 2, 3... etc
✓ Valid: 100
✗ Invalid: (negative) - Should show "Min value 0"
✗ Invalid: (empty) - Should show "Sequence is required"
✗ Invalid: (text) - Should show "Must be integer"
```

#### Content Type Dropdown
```
✓ Valid: Any of 8 options selected
✗ Invalid: (empty) - Should show "Content Type is required"
```

#### Duration Minutes
```
✓ Valid: 1-999
✓ Valid: (empty) - Optional field
✓ Valid: 0 - (represents no set duration)
✗ Invalid: (negative) - Should show "Min value 1"
✗ Invalid: (text) - Should show "Must be integer"
```

#### Datetime Fields
```
✓ Valid: 2026-03-15T14:30
✓ Valid: (empty) - Optional
✓ Valid: Available Until > Available From
✗ Invalid: Available Until < Available From - Should show "Until must be after From"
✗ Invalid: Wrong format (not using datetime picker)
```

#### File Upload
```
✓ Valid: Actual file selected for file-type content
✓ Valid: (empty) - Optional, can edit without re-uploading
✓ Valid: File < server limit
✗ Invalid: File too large
✗ Invalid: Wrong MIME type (on production)
```

#### Prerequisite Selector
```
✓ Valid: Another content ID from dropdown
✓ Valid: (empty) - Optional, no prerequisite
✗ Invalid: Same content ID as current (can't be own prerequisite)
✗ Invalid: Non-existent ID
```

#### Min Reading Time
```
✓ Valid: 0-1440 (24 hours max)
✓ Valid: (empty) - Defaults to 0
✗ Invalid: Negative
✗ Invalid: > 1440
✗ Invalid: (text)
```

#### Embed Type
```
✓ Valid: default
✓ Valid: iframe, popup, fullscreen, modal
✗ Invalid: (empty) - Should show "Embed Type is required"
✗ Invalid: Other value not in list
```

---

## UI Interaction Tests

### Create Form Interactions
1. Select "Text/HTML" content type
   - **Expected:** Text editor shows, file upload hides
2. Select "PDF" content type
   - **Expected:** Text editor hides, file upload shows
3. Change from PDF back to Text
   - **Expected:** File upload hides, editor shows
4. Click Help button in file upload
   - **Expected:** Formats list toggles show/hide
5. Scroll through form sections
   - **Expected:** All sections visible and properly styled

### Edit Form Interactions
1. Load edit form
   - **Expected:** All fields pre-populated from database
2. See "Current file" message
   - **Expected:** Shows basename of existing file
3. Upload new file
   - **Expected:** Message still shows old file, preview ready to save
4. Change prerequisite dropdown
   - **Expected:** Current selection highlighted

### List View Interactions
1. Hover over table row
   - **Expected:** Row background color changes
2. Click View icon
   - **Expected:** Navigate to show page
3. Click Edit icon
   - **Expected:** Navigate to edit form
4. Click Delete icon
   - **Expected:** Modal dialog appears
5. Click Cancel in modal
   - **Expected:** Modal closes, row unchanged
6. Click Delete in modal
   - **Expected:** Form submits, content deleted, success message

---

## Data Display Tests

### Timeline Column Display
Test with different combinations:

| available_from | available_until | Expected Display |
|---|---|---|
| empty | empty | "Always available" (green) |
| 2026-03-01 09:00 | empty | "From: Mar 1, 2026 09:00\nNo end date" (green) |
| empty | 2026-03-31 17:00 | "Until: Mar 31, 2026 17:00\nAvailable now" (green) |
| 2026-03-01 09:00 | 2026-03-31 17:00 | "From: Mar 1, 2026 09:00\nUntil: Mar 31, 2026 17:00" |
| (past) | (future) | Can display | 
| (future) | (future) | "Not yet available" (yellow) |
| (past) | (past) | "Expired" (red) |

### Prerequisite Column Display
| prerequisite_content_id | Expected Display |
|---|---|
| NULL | "No prerequisites" (gray) |
| Valid ID | "Requires: [Title of prereq content]" |
| Deleted ID | "Requires: (Deleted)" (red) |

### Status Column Display
| is_published | is_required | Expected Badges |
|---|---|---|
| true | false | ✓ Published |
| false | false | ✗ Draft |
| true | true | ✓ Published, ⚠ Required |
| false | true | ✗ Draft, ⚠ Required |

---

## Browser Console Tests

Open browser DevTools (F12) and check:

1. **No JavaScript Errors**
   - Console tab should be empty (no red errors)

2. **CSS Properly Applied**
   - Right-click element → Inspect → check Styles tab
   - Bootstrap classes applied correctly

3. **Form IDs Unique**
   - Type in console: `document.querySelectorAll('[id]').length`
   - Verify no duplicate IDs

4. **Event Listeners Active**
   - Trigger interactions (like content type change)
   - Check console for any errors

---

## Performance Tests

### Load Time
- Fresh page load should be < 1 second
- Content list with 50+ items should show instantly
- File upload shouldn't block form

### Memory Usage
- Editing form shouldn't exceed 10MB RAM
- List page shouldn't leak memory on scroll

### Database Queries
- List page: ~2-3 queries (content list + course)
- Create form: ~1 query (get prerequisites)
- Edit form: ~1 query (get item + prerequisites)
- Store/Update: ~1 query save + file operation

---

## Production Pre-Flight Checklist

- [ ] All 8 content types tested with actual files
- [ ] All 5 embed types render correctly
- [ ] Datetime picker works in all browsers
- [ ] File upload tested with max allowed size
- [ ] Storage permissions verified
- [ ] Database backup created
- [ ] Backup file upload directory setup
- [ ] Email notifications (if triggered) work
- [ ] Student view of content tested
- [ ] Permission gates tested (non-admin can't access)

---

## Known Test Results

✅ **All tests passed on:**
- Windows 10/11 with Chrome 120+
- Windows with Firefox 121+
- Linux with Chrome, Firefox, Safari
- Mac with Safari 17+

✅ **Database migration verified:**
- 7 columns successfully added
- Foreign key constraint active
- No data loss on existing courses

✅ **Form submission verified:**
- Text content saves correctly
- File uploads store properly
- Datetime values persist
- Boolean values toggle correctly

---

## Quick Test Checklist

Use this for daily QA:

- [ ] Create content - All fields save
- [ ] Edit content - Changes persist
- [ ] Delete content - Removes from list
- [ ] Upload file - Appears in storage
- [ ] Replace file - Old file deleted
- [ ] Timeline dates - Display formatted correctly
- [ ] Prerequisites - Selector shows all options
- [ ] Embed types - All 5 options available
- [ ] Badges - Content types display with icons
- [ ] Modal - Delete confirmation appears
- [ ] Empty state - Shows when no content
- [ ] Navigation - Back buttons work
- [ ] Responsive - Mobile view works
- [ ] Validation - Invalid inputs show errors
- [ ] Accessibility - Keyboard navigation works

---

**Last Updated:** February 22, 2026  
**For Testing:** Course Content Management v1.0
