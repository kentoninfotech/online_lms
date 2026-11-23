# Flatpickr Calendar Picker - Automated Testing Guide

## Quick Test Steps

### Step 1: Manual Browser Test (Fastest)

1. **Open the application**:
   ```
   https://nountutor.com.ng/admin/courses/3/lessons/create
   ```

2. **Log in with admin credentials**:
   - Email: `lmsadmin@nountutor.com.ng`
   - Password: `@@GLMSAdmin22`

3. **Test the calendar picker**:
   - Locate the "Start Time (Africa/Lagos - UTC+1)" field
   - **Expected input type**: `<input type="datetime-local" id="start_time_picker" ...>`
   - Click on the input field
   - **Expected result**: A Flatpickr calendar popup should appear below/near the input

4. **Verify calendar appearance**:
   - Calendar should show:
     - Month/Year navigation
     - Day grid with selectable dates
     - Time picker (hours:minutes)
     - Close button
   - Calendar z-index should be high (visible above all other elements)
   - Position should not be cut off by page edges

5. **Test date/time selection**:
   - Click on a date (e.g., today or any date in current month)
   - Click on time fields and adjust hours/minutes
   - Click outside or press Escape to close calendar
   - **Expected result**: Selected date/time appears in field as DD/MM/YYYY HH:mm format
     - Example: `16/10/2026 15:00`

### Step 2: Developer Console Verification

If calendar doesn't appear, open Developer Tools:

1. **Press F12** to open DevTools
2. **Go to Console tab**
3. **Type and execute**: `typeof flatpickr`
   - ✅ **Result "function"** = Flatpickr library loaded correctly
   - ❌ **Result "undefined"** = Flatpickr failed to load from CDN

4. **Check for JavaScript errors**:
   - Look for red error messages in Console
   - Pay attention to:
     - CORS/Network errors loading flatpickr CDN
     - TypeError about flatpickr not being a function
     - Issues with initialization script

5. **Verify input initialization**:
   ```javascript
   // Run in console to check if Flatpickr found the input
   document.querySelectorAll('input[type="datetime-local"]').forEach(elem => {
       console.log('Input ID:', elem.id, 'Initialized:', elem.hasAttribute('data-flatpickr-initialized'));
   });
   ```
   - Should show: `Input ID: start_time_picker Initialized: true`

6. **Check Flatpickr instances**:
   ```javascript
   // Run in console to see all active Flatpickr instances
   console.log('Flatpickr instances:', window.flatpickr);
   ```

### Step 3: Inspect Element Analysis

Right-click on the "Start Time" input field:

1. Select **Inspect** or **Inspect Element**
2. Verify the HTML structure:
   ```html
   <input type="datetime-local" 
          id="start_time_picker" 
          name="start_time_picker" 
          class="form-control" 
          placeholder="DD/MM/YYYY HH:mm" 
          data-flatpickr-enabled="true"
          data-flatpickr-initialized="true">
   ```
   - ✅ `type="datetime-local"` - Correct input type
   - ✅ `id="start_time_picker"` - Matches label `for="start_time_picker"`
   - ✅ `data-flatpickr-initialized="true"` - Script recognized and initialized it
   - ❌ `type="text"` - Would NOT trigger Flatpickr initialization

3. Check the hidden input below it:
   ```html
   <input type="hidden" name="start_time" id="start_time">
   ```
   - This receives the final value when form is submitted

### Step 4: Network Tab Check

1. **Open DevTools → Network tab**
2. **Reload the page** (F5)
3. **Filter by "flatpickr"**
4. Verify these resources loaded successfully (Status 200):
   - `flatpickr.min.css` (from CDN: jsdelivr.net)
   - `light.css` (Flatpickr theme)
   - `flatpickr.min.js` (from CDN: jsdelivr.net)

If any show ❌ error status, the CDN is blocked/unavailable.

---

## Expected Test Results

### ✅ Success Scenario
```
Calendar Picker Status: WORKING ✅
├─ Flatpickr loaded: YES (typeof flatpickr = "function")
├─ Calendar popup appears: YES (visible when clicking input)
├─ Date selection works: YES (updates field in DD/MM/YYYY HH:mm format)
├─ Time selection works: YES (can adjust hours and minutes)
├─ Format validation: YES (backend accepts "16/10/2026 15:00" format)
└─ No console errors: YES (check console tab for red errors)
```

### ❌ Failure Scenarios

**Scenario 1: Calendar doesn't appear but input accepts text**
```
- typeof flatpickr = "function" ✅
- Calendar doesn't show when clicking ❌
- Manual text input works: YES

Likely cause: CSS visibility issue or z-index problem
Fix: Check CSS rules affecting .flatpickr-calendar visibility
     Verify z-index: 99999 is applied (inspect in DevTools)
```

**Scenario 2: Flatpickr not loaded at all**
```
- typeof flatpickr = "undefined" ❌
- No calendar, no date picker ❌
- Console errors about CDN loading

Likely cause: CDN blocked, network error, or script load failed
Fix: Check Network tab for failed requests to jsdelivr.net
     Try loading CDN directly in browser address bar
     Check company firewall/proxy blocking CDN access
```

**Scenario 3: Input type is "text" instead of "datetime-local"**
```
- Input type in HTML: type="text" ❌
- Flatpickr initialization doesn't run automatically

Likely cause: Template using old type="text" instead of type="datetime-local"
Fix: Verify add-lesson.blade.php uses type="datetime-local" for start_time_picker
     After fix, hard-refresh browser (Ctrl+F5) to clear cache
```

---

## Test Checklist

### Pre-Test Verification
- [ ] Ensure you're testing with latest code from current branch
- [ ] Hard-refresh browser: **Ctrl+F5** (not just F5)
- [ ] Clear browser cache if experiencing issues
- [ ] Disable browser extensions (they can interfere with Flatpickr)

### Calendar Popup Tests
- [ ] Click on "Start Time" input field → Calendar appears
- [ ] Calendar shows current month/year correctly
- [ ] Can navigate to previous/next month using arrows
- [ ] Can click on any date in the calendar
- [ ] Can see and adjust time (hours/minutes)
- [ ] Calendar closes after selecting date
- [ ] Selected date appears in input in DD/MM/YYYY HH:mm format

### Format Validation Tests
- [ ] Manual text entry: Type `16/10/2026 15:00` → Format accepted
- [ ] Invalid format: Type `2026-10-16` → Should show error
- [ ] Invalid format: Type `16-10-2026 15:00` → Should show error
- [ ] Manual time only: Type `15:00` → Should show error (needs full datetime)

### Cross-Browser Tests
- [ ] Chrome/Chromium browser ✓
- [ ] Firefox browser ✓
- [ ] Safari browser ✓
- [ ] Edge browser ✓
- [ ] Mobile Safari (iOS) - may show native picker
- [ ] Chrome Mobile (Android) - may show native picker

### Different Forms
- [ ] `/admin/courses/3/lessons/create` → Start Time and End Time pickers
- [ ] `/admin/courses/create` → Date fields (if applicable)
- [ ] `/lesson/add` → Date/Time fields (for instructors)
- [ ] Edit forms for lessons and courses

---

## What to Report

Document your findings with:

1. **Calendar Popup Status**: YES / NO
   - If YES: Describe calendar appearance and functionality
   - If NO: Document symptoms (no response, error message, etc.)

2. **Date Format**: What format appears in field after selection?
   - Expected: `DD/MM/YYYY HH:mm` (e.g., `16/10/2026 15:00`)
   - Note if different

3. **Console Errors**: Any red errors in DevTools Console?
   - Take screenshot of console
   - Note full error message

4. **Screenshots**:
   - Before clicking (empty field)
   - Calendar popup when visible
   - After selecting date/time
   - Any error messages shown

5. **Browser/Device**:
   - Browser name and version
   - Operating System
   - Mobile or Desktop

---

## Debugging Commands

Run these in browser DevTools Console if tests fail:

```javascript
// Check Flatpickr library
console.log('Flatpickr loaded:', typeof flatpickr);
console.log('Flatpickr version:', typeof flatpickr.version !== 'undefined' ? flatpickr.version : 'N/A');

// List all datetime inputs
console.log('DateTime inputs found:');
document.querySelectorAll('input[type="datetime-local"]').forEach((el, i) => {
    console.log(`${i}: ID=${el.id}, initialized=${el.hasAttribute('data-flatpickr-initialized')}`);
});

// Check first datetime input specifically
const input = document.getElementById('start_time_picker');
console.log('start_time_picker details:', {
    exists: !!input,
    type: input?.type,
    id: input?.id,
    initialized: input?.hasAttribute('data-flatpickr-initialized'),
    classList: input?.className,
    placeholder: input?.placeholder
});

// Try manually initializing if auto-init failed
if (typeof flatpickr !== 'undefined' && input) {
    console.log('Attempting manual Flatpickr initialization...');
    flatpickr(input, {
        enableTime: true,
        dateFormat: "d/m/Y H:i",
        time_24hr: true,
        minuteIncrement: 1
    });
    console.log('Manual initialization complete');
}

// Check CSS z-index
const calendar = document.querySelector('.flatpickr-calendar');
if (calendar) {
    const zIndex = window.getComputedStyle(calendar).zIndex;
    console.log('Calendar z-index:', zIndex);
} else {
    console.log('No calendar element found in DOM');
}
```

---

## Success Criteria

The feature is working correctly when:

✅ Calendar popup appears when clicking the "Start Time" input  
✅ Calendar displays dates, times, and navigation controls  
✅ User can select date and time from calendar  
✅ Selected date/time appears in correct format: DD/MM/YYYY HH:mm  
✅ No red errors in DevTools Console  
✅ Form can be submitted with selected datetime  
✅ Backend receives and processes the datetime correctly  

---

## Documentation References

- **Flatpickr Official Docs**: https://flatpickr.js.org/
- **Implementation File**: `resources/views/layouts/app.blade.php` (lines 142-230)
- **Form Template**: `resources/views/admin/course-lessons/create.blade.php` (lines ~75-84)
- **Format Rule**: Laravel validation `date_format:d/m/Y H:i`

