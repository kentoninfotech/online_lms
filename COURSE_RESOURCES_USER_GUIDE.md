# Course Resources - Quick Start Guide

## For Instructors & Admins

### Creating Course Content with Resources

1. **Navigate** to Admin → Courses → Content → Create
   - Or: `/admin/courses/{id}/content/create`

2. **Fill in Basic Information**
   - Content Title
   - Description
   - Content Type (Text, Video, PDF, etc.)
   - Sequence (display order)

3. **Add Course Content Resources** (New Section!)
   - Scroll to "Course Content Resources" section
   - Click **"Add Another Resource"** button
   - For each resource, fill in:
     - **Resource Title**: Name of the file (e.g., "Lecture Notes")
     - **Resource Description**: Brief description (optional)
     - **Select File**: Upload PDF, Word, Excel, PowerPoint, ZIP, or TXT files
   - Add as many resources as needed
   - Each resource can be up to 100MB
   - Remove individual resources with the **Remove** button

4. **Save & Publish**
   - Click **Create Content**
   - Resources are automatically published and visible to enrolled students

### Editing Content & Resources

1. **Navigate** to Admin → Courses → Content → Edit
   - Or: `/admin/courses/{id}/content/{content}/edit`

2. **Manage Existing Resources**
   - View all current resources in "Current Resources" section
   - Each resource shows:
     - File icon and name
     - File size
     - Download count
     - **Download** button to preview
     - **Delete** button to remove (with confirmation)

3. **Add New Resources**
   - Scroll to "Add New Resources" section
   - Follow same steps as creating
   - Multiple new resources can be added at once

4. **Save**
   - Click **Save Changes**
   - New resources are immediately available to students

## For Students & Enrollees

### Accessing Course Content & Resources

1. **Go to Course Learning Page**
   - From your course page, click **Learn** or go to `/course/{course_id}/learn`

2. **View Course Content**
   - See list of all course materials in the sidebar (left side)
   - On mobile, click **"Course Content"** button to expand
   - Click any content to view it

3. **View & Download Resources**
   - After opening content, scroll to **"Course Resources"** section (blue header)
   - See table with all resources for this content:
     - **Resource name** with icon and description
     - **File size** (formatted as KB, MB, etc.)
     - **Download count** (how many times it's been downloaded)
     - **Download button** to get the file

4. **Download Process**
   - Click **Download** next to any resource
   - File downloads to your device
   - System tracks download (count increases)
   - You must be an enrolled student to download

### Navigation Features

- **Breadcrumb** at top shows: Course Name → Content Title
- **Sidebar** (left side) shows:
  - All course contents in order
  - Your progress percentage
  - Collapse on mobile to save space
- **Resources** organized in table format for easy access

## Common Tasks

### Upload Multiple Resources at Once
1. Click "Add Another Resource" for each file
2. All resources are queued in the form
3. Click Create/Save once to upload all

### Delete a Resource
1. Go to Edit Content page
2. Find resource in "Current Resources" section
3. Click **Delete** button
4. Confirm deletion
5. Click **Save Changes** (optional - deletes immediately)

### Check Download Statistics
- In Edit view, see download count for each resource
- Helps track student engagement

### Change Resource Order
- Not currently sortable in UI
- Resources display by sequence number
- Can edit sequence in database if needed

## Supported File Types
✅ PDF (.pdf)
✅ Word (.doc, .docx)
✅ Excel (.xls, .xlsx)
✅ PowerPoint (.ppt, .pptx)
✅ Text (.txt)
✅ Archives (.zip)
❌ Images, Videos (use main content upload instead)

## Troubleshooting

**Q: File won't upload**
- Check file size (max 100MB per file)
- Verify file type is supported
- Ensure permissions allow file upload

**Q: Students can't download resources**
- Verify students are enrolled with "active" status
- Check resource is published (is_published = true)
- Verify file still exists in storage

**Q: Sidebar taking up too much space**
- On desktop: This is normal, sidebar is part of the layout
- On mobile: Click "Course Content" button to collapse sidebar
- Use the collapse button (top right of sidebar)

**Q: Resources not showing**
- Publish the content (is_published checkbox)
- Publish the resource (should be automatic)
- Reload page to refresh

## Tips & Best Practices

✅ **DO:**
- Use descriptive resource titles
- Add descriptions explaining what each file contains
- Keep file sizes reasonable (compress when possible)
- Delete old/unused resources
- Check download counts to see what students use

❌ **DON'T:**
- Upload files larger than 100MB
- Upload unsupported file types (use course content for media)
- Keep duplicate resources
- Publish incomplete or test files

## Security Notes
- Only enrolled students can download resources
- Files are stored securely on server
- Downloads are tracked by system
- Admins and instructors have full control
- Soft deletes preserve history

---
**Need Help?** Contact your administrator or check the main documentation.
