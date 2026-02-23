# Course Content Management - Quick Admin Guide

## Overview
The updated Course Content Management system allows instructors and admins to create, manage, and track course learning materials with advanced features like:
- Timeline-based availability
- Content prerequisites  
- Multiple content formats
- Student engagement tracking
- Flexible display modes

## Quick Start

### Accessing Course Content Management

1. Go to **Courses** → Select a course → Click **"Manage Content"** button
2. You'll see the Content List page showing all materials in the course

### Creating New Content

1. Click **"Add Content"** button
2. Fill in the form:

#### Basic Information
- **Title** *(required)* - Name of the learning material
- **Description** - Brief overview of the content
- **Content Type** *(required)* - Choose from:
  - Text/HTML - Write or paste content directly
  - Video - Upload or link video file
  - PDF - Upload document
  - Word - Upload .doc or .docx
  - PowerPoint - Upload presentation
  - Excel - Upload spreadsheet
  - Image - Upload image file
  - Link - External URL
- **Sequence** *(required)* - Display order (0, 1, 2...)
- **Duration** - How many minutes students should spend (optional)

#### Add Content or Upload File
- **For Text Type:** Write content in the editor using HTML tags
- **For File Types:** Click "Upload File" and select from your computer
  - Supported: PDF, Word docs, PowerPoint, Excel, Images

#### Timeline & Availability (Optional)
- **Available From** - When students can start accessing (leave empty = immediately)
- **Available Until** - When access expires (leave empty = never expires)
- **Min Reading Time** - How long student must spend before marking complete

#### Prerequisites (Optional)
- **Prerequisite Content** - Pick another content that must be completed first
- Leave blank if no prerequisites

#### Display & Tracking
- **How to Display Content** - Choose display mode:
  - Default - Normal page display
  - IFrame - Embedded in container
  - Popup - Opens in popup window
  - Full Screen - Takes entire screen
  - Modal - Dialog box display
- **Track Student Viewing Time** - Check to log how long students engage
- **Allow Students to Download** - Check to let students save files locally

#### Status
- **Publish** - Check to make visible to students (leave unchecked = draft)
- **Mark as Required** - Check if students must complete to finish course

3. Click **"Create Content"**

### Editing Content

1. Find content in the list
2. Click the **edit icon** (pencil)
3. Modify any fields
4. To replace a file: Upload new file (old file automatically deleted)
5. Click **"Save Changes"**

### Viewing Content

1. Find content in the list
2. Click the **view icon** (eye) to preview

### Deleting Content

1. Find content in the list
2. Click the **delete icon** (trash)
3. Confirm deletion in popup
4. Content and associated files are removed

## Understanding the Content List

The table shows key information:

| Column | Meaning |
|--------|---------|
| # | Display order |
| Title | Content name, description excerpt, and reading time |
| Type | Content format with colored badge |
| Status | Published (green) or Draft (gray), Required indicator |
| Timeline | When available - "From X Until Y" or "Always available" |
| Prerequisites | What content must be completed first, if any |
| Actions | View/Edit/Delete buttons |

## Common Scenarios

### Scenario 1: Video Lesson with Prerequisites
1. Create content with Content Type = Video
2. Upload video file
3. Set prerequisite to a foundational video
4. Set min reading time = 5 minutes
5. Publish
6. Result: Students must watch prerequisite first, then must spend 5+ minutes on this video

### Scenario 2: Reading Material Available for Limited Time
1. Create content with Content Type = Text/HTML
2. Write or paste content  
3. Set Available From = Start of lesson week
4. Set Available Until = End of week
5. Publish
6. Result: Students can only access during that week

### Scenario 3: Optional Supplementary Material
1. Create content
2. Leave "Mark as Required" unchecked
3. Don't set prerequisites
4. Publish
5. Result: Available to all students but optional

### Scenario 4: Downloadable Reference Guide
1. Create content with Content Type = PDF
2. Upload PDF file
3. Check "Allow Students to Download"
4. Check "Track Student Viewing Time"
5. Set Min Reading Time = 10 minutes
6. Publish
7. Result: Students can download PDF and their engagement is tracked

## Tips & Best Practices

### Content Organization
- Use sequence numbers to create logical flow
- Number consecutively: 0, 1, 2, 3...
- Group related content with consistent numbering

### Student Engagement
- Set realistic minimum reading times (based on content length)
- Enable tracking on important materials only
- Use prerequisites to enforce learning path

### File Management
- Keep file names short and descriptive
- Use consistent naming: "Week1_Lesson1_Introduction.pdf"
- Files are stored securely on server
- Old files auto-delete when you upload replacements

### Availability Control
- Set "Available From" at week start if using weekly content
- Set "Available Until" for time-sensitive materials
- Leave both blank for always-available reference materials

### Display Modes
- Use **Default** for standard text/content
- Use **IFrame** for embedded content (safe third-party)
- Use **Popup** for supplementary information
- Use **Full Screen** for important videos or presentations
- Use **Modal** for quick reference info

## Troubleshooting

### File Upload Issues
- Check file format is supported for content type
- File size should be under server limit
- Ensure file name doesn't have special characters

### Content Not Showing to Students
- Check if content is Published (✓ next to Publish)
- Check if currently within availability window (if set)
- Check if prerequisites are met (if set)

### Tracking Not Working
- Ensure "Track Student Viewing Time" is enabled
- Student must properly complete viewing before tracking records
- Check student learning hub to see tracked time

## Keyboard Shortcuts
- Save form: Ctrl+Enter
- Delete with confirmation: Ctrl+D (in view)

## Need Help?
- Check COURSE_CONTENT_MANAGEMENT_IMPLEMENTATION.md for technical details
- Review database schema changes for field descriptions
- Examine view files for available features

---
**Last Updated:** Feb 22, 2026
**System Version:** Course Content Management v1.0
