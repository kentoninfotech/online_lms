# Flatpickr Calendar Picker Test Instructions

## Test Objective
Verify that the Flatpickr calendar picker is now working on the lesson creation form.

## Pre-Test Checklist
- [ ] You have valid login credentials: `lmsadmin@nountutor.com.ng` / `@@GLMSAdmin22`
- [ ] The production site is accessible at `https://nountutor.com.ng`
- [ ] Browser DevTools are open (F12)

## Test Steps

### Step 1: Navigate to Lesson Creation Form
1. Go to: https://nountutor.com.ng/admin/courses/3/lessons/create
2. Log in if prompted with credentials: `lmsadmin@nountutor.com.ng` / `@@GLMSAdmin22`
3. You should see the lesson creation form with:
   - Subject dropdown
   - Instructor selection
   - Student selection
   - **Start Time field** (this is what we're testing)
   - Duration field
   - Recurrence options

### Step 2: Check Flatpickr Library is Loaded
1. Open Browser DevTools (F12)
2. Go to the **Console** tab
3. Type: `typeof flatpickr` and press Enter
4. **Expected Result:** Should show `"function"` (NOT `"undefined"`)
5. If it shows `"undefined"`, Flatpickr is not loaded and the test fails

### Step 3: Verify CSS is Loaded
1. Go to the **Network** tab in DevTools
2. Filter by `flatpickr`
3. You should see:
   - ✓ `flatpickr.min.css` - Status 200
   - ✓ `light.css` (Flatpickr theme) - Status 200
   - ✓ `flatpickr.min.js` - Status 200
4. If any are missing or show Status 404, there's a loading problem

### Step 4: Click on Start Time Field
1. Click on the **Start Time** input field
2. **Expected Result:** A calendar picker popup should appear
   - The popup should show a date/time picker
   - It should display the current month/year
   - It should have time picker controls
   - The popup should be styled with Flatpickr CSS

3. **If nothing appears:**
   - Go to DevTools Console
   - Look for red error messages
   - Report any `Flatpickr` or JavaScript errors

### Step 5: Test Date Selection
1. In the calendar popup that appeared:
   - Click on a date (e.g., day 15 of the current month)
   - You should see the date highlighted/selected

2. **Expected Result:** 
   - Date should be shown as `DD/MM/YYYY` in the input field
   - Example: `15/10/2026`

### Step 6: Test Time Selection
1. After selecting a date, look for time picker controls in the popup
2. Set a time:
   - Click on the hours field and set to `15`
   - Click on the minutes field and set to `30`

3. **Expected Result:**
   - The input field should show: `15/10/2026 15:30`
   - Format should be: `DD/MM/YYYY HH:mm`

### Step 7: Test Calendar Closing
1. Click outside the calendar popup OR click the selected date again
2. **Expected Result:**
   - Calendar popup should close
   - The selected date/time should remain in the input field

### Step 8: Check Console for Errors
1. Go to DevTools **Console** tab
2. Look for any error messages (red text)
3. **Expected:** No errors related to Flatpickr or date picking
4. **Common errors to look for:**
   - "flatpickr is not defined"
   - "Cannot read property 'flatpickr' of undefined"
   - Any CORS errors for CDN resources

### Step 9: Verify Hidden Field is Updated
1. After selecting a date/time (e.g., `16/10/2026 15:00`)
2. Open DevTools **Inspector**
3. Find the input field with `id="start_time_picker"`
4. Look for the hidden input field `name="start_time"` right after it
5. **Expected:** The hidden field should have the converted backend format value
6. **Check the value:** Right-click → Inspect → look at the `value` attribute

### Step 10: Test with Different Times
1. Click the Start Time field again
2. Select a different time (e.g., `20/10/2026 09:00`)
3. **Expected Result:** 
   - Calendar popup appears again
   - You can select new date/time
   - Input field updates with new value
   - No console errors

## Success Criteria
The test is **PASSED** if:
- ✓ Flatpickr library loads (typeof flatpickr === 'function')
- ✓ Calendar popup appears when clicking Start Time field
- ✓ Date selection works (date appears in input field as DD/MM/YYYY)
- ✓ Time selection works (time appears in input field as HH:mm)
- ✓ Full format is `DD/MM/YYYY HH:mm`
- ✓ Calendar closes after selection
- ✓ No JavaScript errors in console
- ✓ Hidden field is updated with backend format

## Test Results Template
Copy and paste the results below:

```
Test Date: ___________
Tester: ___________

Step 2 - Flatpickr Loaded: ☐ YES ☐ NO
Step 3 - CSS Loaded: ☐ YES ☐ NO
Step 4 - Calendar Popup Appears: ☐ YES ☐ NO
Step 5 - Date Selection Works: ☐ YES ☐ NO
Step 6 - Time Selection Works: ☐ YES ☐ NO
Step 7 - Calendar Closes Properly: ☐ YES ☐ NO
Step 8 - No Console Errors: ☐ YES ☐ NO
Step 9 - Hidden Field Updated: ☐ YES ☐ NO
Step 10 - Different Times Work: ☐ YES ☐ NO

Overall Result: ☐ PASSED ☐ FAILED

Notes/Issues:
_____________________________________________________________________
_____________________________________________________________________
```

## Troubleshooting

### Calendar Not Appearing
**Cause:** Flatpickr library might not be loaded
**Solution:**
1. Check DevTools Console: `typeof flatpickr`
2. Check Network tab for Flatpickr JS file
3. If missing, the layout file needs to be fixed

### Wrong Date Format
**Cause:** Flatpickr configuration might be incorrect
**Solution:**
1. Check the `dateFormat: "d/m/Y H:i"` in app.blade.php
2. Verify the configuration is applied to `datetime-local` inputs

### JavaScript Errors
**Cause:** Conflicts with other libraries or missing dependencies
**Solution:**
1. Note the exact error message
2. Check DevTools Console for full stack trace
3. Report the error with a screenshot

## Performance Baseline
- Flatpickr library load time: <1 second
- Calendar popup response: <200ms
- Date selection: Instant

## Browser Compatibility
- Chrome/Chromium: ✓ Supported
- Firefox: ✓ Supported
- Safari: ✓ Supported
- Edge: ✓ Supported
