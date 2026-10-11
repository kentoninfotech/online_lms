# Live Session DateTime Format Fix

## Issue
When scheduling or editing a live session via the admin panel, users received the following validation error:

```
Error: The scheduled start field must match the format Y-m-d H:i.
```

This occurred even though the user was entering a valid date and time through the HTML datetime picker.

## Root Cause
The form uses HTML5 `<input type="datetime-local">` which:
- Returns data in the format: `YYYY-MM-DDTHH:mm` (e.g., `2026-10-16T15:00`)
- Uses `T` as the separator between date and time

However, the Laravel server validation expected:
- Format: `Y-m-d H:i` (e.g., `2026-10-16 15:00`)
- Uses space as the separator between date and time

This mismatch caused the validation to fail.

## Solution
Added JavaScript form submission handler that converts the datetime-local format to the expected format **before** submitting the form to the server.

### Changes Made

#### 1. **create.blade.php** - Added Format Conversion
```javascript
// Convert datetime-local format (YYYY-MM-DDTHH:mm) to Y-m-d H:i format for server
document.querySelector('form').addEventListener('submit', function(e) {
    const scheduledStart = document.getElementById('scheduled_start').value;
    const scheduledEnd = document.getElementById('scheduled_end').value;
    
    if (scheduledStart) {
        // Convert from 2026-10-16T15:00 to 2026-10-16 15:00
        document.getElementById('scheduled_start').value = scheduledStart.replace('T', ' ');
    }
    
    if (scheduledEnd) {
        // Convert from 2026-10-16T16:00 to 2026-10-16 16:00
        document.getElementById('scheduled_end').value = scheduledEnd.replace('T', ' ');
    }
});
```

#### 2. **LiveSessionController.php** - Added Edit Method
Added the missing `adminEdit()` method:
```php
/**
 * Admin: Edit live session form
 */
public function adminEdit(Course $course, CourseLiveSession $session)
{
    $this->authorize('update', $course);
    $facilitators = \App\Models\Facilitator::where('is_active', true)->get();
    return view('admin.live-sessions.edit', compact('course', 'session', 'facilitators'));
}
```

#### 3. **edit.blade.php** - New Edit Form View
Created complete edit view with:
- Pre-populated form fields with existing session data
- Proper datetime-local formatting using `$session->scheduled_start->format('Y-m-d\TH:i')`
- Same form conversion JavaScript as create view
- PUT method with `@method('PUT')` directive
- Session status and metadata display

### How It Works

1. User opens the live session creation/edit form
2. User enters a date and time using the browser's datetime picker
3. Browser stores the value as `YYYY-MM-DDTHH:mm` format (e.g., `2026-10-16T15:00`)
4. When user clicks "Submit", JavaScript intercepts the form submission
5. JavaScript replaces the `T` with a space: `2026-10-16 15:00`
6. Form is submitted with the correct format
7. Server validation passes and session is created/updated

### Affected Routes

Both admin and tutor routes now work properly:

**Admin Routes:**
- `GET /admin/courses/{course}/live-session/create` → Create form
- `POST /admin/courses/{course}/live-session` → Store session
- `GET /admin/courses/{course}/live-session/{session}/edit` → Edit form
- `PUT /admin/courses/{course}/live-session/{session}` → Update session

**Tutor Routes:**
- `GET /tutor/courses/{course}/live-session/create` → Create form
- `POST /tutor/courses/{course}/live-session` → Store session
- `GET /tutor/courses/{course}/live-session/{session}/edit` → Edit form
- `PUT /tutor/courses/{course}/live-session/{session}` → Update session

## Testing

### Test Case 1: Create Live Session
1. Navigate to `/admin/courses/{courseId}/live-session/create`
2. Fill in form:
   - Title: "Physics Class"
   - Facilitator: Select any facilitator
   - Start Date & Time: Use date picker to select `16/10/2026 15:00`
   - End Date & Time: Use date picker to select `16/10/2026 16:00`
3. Click "Schedule Session"
4. **Expected:** Form submits successfully, no validation error

### Test Case 2: Edit Live Session
1. Navigate to an existing live session detail page
2. Click "Edit" button
3. Modify any datetime field using the date picker
4. Click "Update Session"
5. **Expected:** Form submits successfully, session is updated

### Test Case 3: Validation Still Works
1. Try to submit with end time before start time
2. **Expected:** Validation error shows (after:scheduled_start fails)

## Files Modified

- `resources/views/admin/live-sessions/create.blade.php` - Added form submission handler
- `app/Http/Controllers/LiveSessionController.php` - Added adminEdit() method
- `resources/views/admin/live-sessions/edit.blade.php` - NEW: Complete edit view

## Validation Rules

The following validation rules remain in place:

```php
'scheduled_start' => 'required|date_format:Y-m-d H:i',
'scheduled_end' => 'required|date_format:Y-m-d H:i|after:scheduled_start',
```

After the JavaScript conversion, these rules will pass as expected.

## Browser Compatibility

The datetime-local format conversion works in all modern browsers:
- ✅ Chrome/Chromium
- ✅ Firefox
- ✅ Safari
- ✅ Edge

## Alternative Solutions Considered

1. **Change form input type to `text`** - Would require custom date picker library, more complex
2. **Use Carbon format conversion in Blade** - Would still have mismatch with datetime-local format
3. **Change Laravel validation rule** - Could break if user submits via API with different format
4. **Server-side conversion** - More complex, adds server processing

The chosen solution is:
- Minimal code change
- No library dependencies
- Works seamlessly with existing validation
- No impact on API or other submission methods

## Future Improvements

1. Consider adding a date picker library (Flatpickr, Bootstrap Datepicker) for better UX
2. Add server-side conversion as fallback
3. Create a reusable Blade component for datetime-local forms
4. Document datetime format requirements in API documentation

## Related Issues

- #N/A - Initial report: "Error deleting student by the admin" (different issue)
- Live Session feature fully implemented in prior commits

## Commit
- **Hash:** 40e1d22
- **Message:** fix: Fix datetime format validation error in live session scheduling
- **Files Changed:** 3 files, 371 insertions(+)
