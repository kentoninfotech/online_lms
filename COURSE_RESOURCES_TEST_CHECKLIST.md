# Course Resources Implementation - Testing Checklist

## Pre-Testing Setup

- [ ] Database migration completed successfully
- [ ] All new files created without syntax errors
- [ ] Laravel cache cleared: `php artisan view:cache`
- [ ] Application in development mode for debugging

## Model Tests

### CourseContentResource Model
- [ ] Model created at `app/Models/CourseContentResource.php`
- [ ] Relationships working:
  - [ ] `content()` returns associated CourseContent
  - [ ] `getCourse()` returns associated Course
- [ ] Methods functioning:
  - [ ] `getFileUrl()` returns correct asset path
  - [ ] `getHumanReadableSize()` formats sizes correctly (B, KB, MB, GB)
  - [ ] `getIconClass()` returns Bootstrap icon class
  - [ ] `incrementDownloadCount()` increases counter

### CourseContent Model Updates
- [ ] `resources()` relationship added
- [ ] `publishedResources()` relationship returns filtered, ordered results

## Controller Tests

### CourseContentResourceController
- [ ] File located at `app/Http/Controllers/CourseContentResourceController.php`
- [ ] `download()` method:
  - [ ] Only accessible to enrolled students (active status)
  - [ ] Increments download count
  - [ ] Returns correct file
  - [ ] File downloads with correct name
- [ ] `destroy()` method:
  - [ ] Only accessible to authorized users (admin/instructor)
  - [ ] Deletes resource record
  - [ ] Deletes file from storage
  - [ ] Redirects to edit page with success message

### CourseContentController Updates
- [ ] `adminStore()` accepts resource uploads
- [ ] `adminUpdate()` accepts resource uploads
- [ ] `storeResources()` private method:
  - [ ] Creates resource records
  - [ ] Stores files in correct directory
  - [ ] Handles multiple files
  - [ ] Saves file metadata (type, size, mime)

## View Tests

### Create Form (`resources/views/admin/course-contents/create.blade.php`)
- [ ] "Course Content Resources" section visible
- [ ] Can add resources with title, description, file
- [ ] "Add Another Resource" button adds new input set
- [ ] Remove buttons delete resource inputs
- [ ] JavaScript console has no errors
- [ ] Form submits with resources

### Edit Form (`resources/views/admin/course-contents/edit.blade.php`)
- [ ] "Current Resources" section shows existing resources
- [ ] Each resource shows file icon, name, size, downloads
- [ ] Download button works for each resource
- [ ] Delete button shows confirmation
- [ ] Delete removes resource
- [ ] "Add New Resources" section works like create form
- [ ] Can add multiple new resources
- [ ] Form submits changes

### Learning Content Page (`resources/views/courses/learn/content.blade.php`)
- [ ] Breadcrumb displays: "Course Name → Content Title"
- [ ] Breadcrumb links work correctly
- [ ] Sidebar collapses on mobile devices
- [ ] "Course Content" toggle button visible on mobile
- [ ] Sidebar expands/collapses correctly
- [ ] Course content list displays correctly
- [ ] Progress bar shows accurate percentage
- [ ] "Course Resources" section visible if resources exist
- [ ] Resources table displays:
  - [ ] File icon
  - [ ] Title and description
  - [ ] File size (human-readable)
  - [ ] Download count
  - [ ] Download button
- [ ] Download button links work
- [ ] No resources message if section empty

## Route Tests

### New Routes
- [ ] `GET /course-content-resources/{resource}/download` registered
- [ ] `DELETE /course-content-resources/{resource}` registered
- [ ] Routes have correct names
- [ ] Routes redirect correctly

## Policy Tests

### CourseContentResourcePolicy
- [ ] Admin can view any resource
- [ ] Assigned instructor can view resources
- [ ] Enrolled student can view resources
- [ ] Non-enrolled user cannot view resources
- [ ] Admin can delete any resource
- [ ] Assigned instructor can delete resources
- [ ] Other users cannot delete resources
- [ ] Policy registered in AuthServiceProvider

## Database Tests

### course_content_resources Table
- [ ] Table created by migration
- [ ] All columns present and correct type
- [ ] Foreign key to course_contents works
- [ ] Indexes created on:
  - [ ] course_content_id
  - [ ] sequence
  - [ ] is_published

## File Upload Tests

### During Content Creation
- [ ] Single resource file uploads
- [ ] Multiple resource files upload
- [ ] File appears in storage directory
- [ ] File path saved correctly in database
- [ ] File accessible via public URL

### During Content Edit
- [ ] Can add new resources to existing content
- [ ] New files upload correctly
- [ ] Existing resources remain unchanged
- [ ] Files can be deleted
- [ ] File deleted from storage when record deleted

### File Handling
- [ ] Files stored in: `storage/app/public/uploads/courses/{course_id}/resources/`
- [ ] File naming works (timestamp + unique ID)
- [ ] Various file types upload:
  - [ ] PDF
  - [ ] Word documents
  - [ ] Excel spreadsheets
  - [ ] PowerPoint presentations
  - [ ] Text files
  - [ ] ZIP archives
- [ ] Large files (up to 100MB) handle properly
- [ ] File permissions correct for public access

## User Experience Tests

### For Instructors/Admins
- [ ] Can create content with resources
- [ ] Can edit content and add resources
- [ ] Can delete individual resources
- [ ] Success messages display
- [ ] Error messages display on validation failure
- [ ] File upload progress visible
- [ ] Can see download statistics

### For Students
- [ ] Can see resources on learning page
- [ ] Can download resources
- [ ] Download counter increments
- [ ] Cannot access student content without enrollment
- [ ] Mobile experience is good (sidebar collapses)
- [ ] Breadcrumbs help navigation
- [ ] Resources table is readable on mobile

## Performance Tests

- [ ] Page loads quickly with many resources
- [ ] File downloads don't freeze interface
- [ ] Database queries optimized (no N+1)
- [ ] Large files don't timeout
- [ ] Multiple simultaneous downloads work

## Security Tests

- [ ] Only enrolled students can download
- [ ] Only admins/instructors can delete
- [ ] File paths are safe (no directory traversal)
- [ ] Downloads are tracked correctly
- [ ] Non-authenticated users redirected to login
- [ ] Invalid IDs return 404/403

## Validation Tests

- [ ] Empty resource titles handled
- [ ] Missing file handled
- [ ] Invalid file types rejected
- [ ] Oversized files rejected
- [ ] Form errors display clearly

## Browser Compatibility

- [ ] Desktop (Chrome, Firefox, Safari, Edge)
  - [ ] Forms work correctly
  - [ ] Download functions
  - [ ] Styling looks good
- [ ] Mobile (iOS Safari, Chrome Mobile)
  - [ ] Sidebar collapses
  - [ ] Tables are readable
  - [ ] Touch interactions work
- [ ] Tablet (iPad, Android tablets)
  - [ ] Layout adapts
  - [ ] No horizontal scrolling

## Documentation Tests

- [ ] README/guide explains feature
- [ ] Code comments are clear
- [ ] No console errors
- [ ] No console warnings (except gd module)

## Edge Cases

- [ ] Creating content with 0 resources works
- [ ] Editing content to remove all resources works
- [ ] Re-uploading same file works
- [ ] Deleting resource and re-adding works
- [ ] Very long resource titles handled
- [ ] Special characters in filenames work
- [ ] Concurrent uploads don't conflict
- [ ] Course with same resources in multiple contents works

## Integration Tests

- [ ] Breadcrumbs work with course navigation
- [ ] Resources work with course completion tracking
- [ ] Resources work with progress calculation
- [ ] Download history could be added in future
- [ ] Email notifications reference resources correctly

## Cleanup & Final Checks

- [ ] All test files removed/cleaned up
- [ ] Database has test data removed
- [ ] No hardcoded paths or IDs in code
- [ ] Code follows project conventions
- [ ] No debug statements left in code
- [ ] Git status shows only new files/intentional changes

---

## Testing Notes

**Test Environment:**
- PHP Version: [Enter version]
- Laravel Version: [Enter version]
- Database: [Enter database type]
- Browser(s): [Enter browsers tested]

**Date Tested:** _______________

**Tested By:** _______________

**Issues Found:** 

---

## Deployment Checklist

- [ ] All tests passed
- [ ] No console errors
- [ ] Database backup created
- [ ] Migration tested on staging
- [ ] File permissions configured
- [ ] Storage directory writable
- [ ] Public directory symlinked properly
- [ ] Cache cleared before deployment
- [ ] Configuration reviewed
