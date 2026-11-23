# Flatpickr Calendar Picker - Quick Reference Guide
**Implementation Date:** 2026-10-12  
**Status:** ✅ READY FOR TESTING

---

## 30-Second Test

1. Go to: https://nountutor.com.ng/admin/courses/3/lessons/create
2. Login: lmsadmin@nountutor.com.ng / @@GLMSAdmin22
3. Click "Start Time" field
4. **Calendar appears?** ✅ → Success

---

## What to Look For

### ✅ Calendar Popup Should:
- Appear within 200ms of clicking
- Show current month and year
- Display 7 columns (Mon-Sun)
- Display dates from previous/current/next month
- Have navigation arrows (< >)
- Have time picker (hours/minutes)
- Close when clicking date
- Close when clicking outside

### ✅ Date Format Should:
- Display as: `DD/MM/YYYY HH:mm`
- Example: `16/10/2026 15:00`
- NOT show as: `2026-10-16` or `10/16/2026`
- NOT show as: `15:00:00` or `3:00 PM`

### ✅ Interactions Should:
- Click date → Selects date
- Click time → Adjusts time
- Click outside → Closes calendar
- Type manually → Accepted as-is
- Form submit → Works without errors

---

## Console Commands (F12)

### Check if Flatpickr Loaded
```javascript
typeof flatpickr
// Expected: "function"
// If: "undefined" → Not loaded, CDN issue
```

### Check Initialization
```javascript
document.querySelector('#start_time_picker')._flatpickr
// Should return Flatpickr instance object
```

### Check Loaded Files
```javascript
// Press F12 → Network tab → Filter "flatpickr"
// Should show 3 items all with status 200 (green)
```

---

## Common Errors & Fixes

| Error | Appears Where | Fix |
|-------|---------------|-----|
| `flatpickr is not defined` | Console | Refresh page, check CDN |
| Calendar hidden | Behind form | Clear browser cache |
| Wrong format | Field shows `YYYY-MM-DD` | Browser cached old version |
| `404` for flatpickr.js | Network tab | CDN is blocked |
| Date won't select | Calendar open | Try different browser |

---

## Browser DevTools Guide

### Opening DevTools
- **Windows/Linux:** `F12` or `Ctrl+Shift+I`
- **Mac:** `Cmd+Option+I`

### Checking Flatpickr Loaded
1. Open DevTools (F12)
2. Go to **Console** tab
3. Type: `typeof flatpickr`
4. Press **Enter**
5. Should show: `"function"` ✅

### Checking Network
1. Open DevTools (F12)
2. Go to **Network** tab
3. Reload page (F5)
4. Type "flatpickr" in filter
5. Should show 3 items:
   - `flatpickr.min.css` → Status 200
   - `light.css` → Status 200
   - `flatpickr.min.js` → Status 200

### Checking Form Submission
1. Open DevTools (F12)
2. Go to **Network** tab
3. Fill form and click "Create Lesson"
4. Find `lesson.store` in requests
5. Click it → Go to **Request** tab
6. Check **Form Data** section
7. Look for: `start_time: 2026-10-16 15:30` (backend format)

### Checking for Errors
1. Open DevTools (F12)
2. Go to **Console** tab
3. Look for red text (errors)
4. Should see NO red messages
5. Optional: See info/debug messages (gray text OK)

---

## Test Checklist

### Before Testing
- [ ] Logged in with: lmsadmin@nountutor.com.ng
- [ ] URL: admin/courses/3/lessons/create
- [ ] Browser up to date
- [ ] JavaScript enabled
- [ ] No ad blocker blocking CDN (or whitelist jsDelivr)

### Quick Test (5 min)
- [ ] Calendar popup appears
- [ ] Can select date
- [ ] Can select time
- [ ] Format is `DD/MM/YYYY HH:mm`
- [ ] No console errors

### Full Test (15 min)
- [ ] Basic date selection ✅
- [ ] Time selection ✅
- [ ] Calendar navigation ✅
- [ ] Manual entry ✅
- [ ] Form submission ✅
- [ ] Console clean ✅
- [ ] Network assets loaded ✅

### Multi-Browser Test (30 min)
- [ ] Chrome/Chromium
- [ ] Firefox
- [ ] Safari (Mac) or IE/Edge (Windows)
- [ ] Mobile browser (if available)

---

## Hidden Inputs Explained

The form has TWO date inputs:

### Visible Input (For User)
```html
<input type="datetime-local" id="start_time_picker" name="start_time_picker">
```
- **What:** Datetime-local input
- **Display:** `DD/MM/YYYY HH:mm` (formatted by Flatpickr)
- **User sees:** Calendar picker when clicking
- **Format:** `16/10/2026 15:00` (user-friendly)

### Hidden Input (For Backend)
```html
<input type="hidden" name="start_time" id="start_time">
```
- **What:** Hidden input
- **Display:** Not visible to user
- **Backend gets:** Auto-converted format
- **Format:** `2026-10-16 15:00` (database format)

### How It Works
```
User clicks Start Time
        ↓
Calendar appears (Flatpickr)
        ↓
User selects date/time
        ↓
Visible input shows: 16/10/2026 15:00
        ↓
JavaScript converts to: 2026-10-16 15:00
        ↓
Hidden input filled automatically
        ↓
Form submits with backend format
        ↓
Database happy ✅
```

---

## Keyboard Shortcuts (In Calendar)

| Key | Action |
|-----|--------|
| **Tab** | Move to next field |
| **Shift+Tab** | Move to previous field |
| **Arrow Up** | Previous month |
| **Arrow Down** | Next month |
| **Arrow Left** | Previous day |
| **Arrow Right** | Next day |
| **Enter** | Select highlighted date |
| **Escape** | Close calendar |

---

## Screenshots to Take

When testing, take screenshots of:

1. **Calendar Open**
   - Showing popup with dates
   - Showing time picker
   - Showing navigation arrows

2. **Date Selected**
   - Field showing `DD/MM/YYYY HH:mm` format
   - Calendar closed

3. **Form Submitted**
   - DevTools Network tab
   - lesson.store request
   - Form Data showing backend format

4. **Console Clean**
   - DevTools Console tab
   - No red error messages
   - Only info/success messages

---

## Scenarios to Test

### Scenario A: Fresh Date Entry
```
1. Page loads
2. Click Start Time
3. Calendar appears ✓
4. Click date 16
5. Set time 15:30
6. Field shows: 16/10/2026 15:30 ✓
7. Submit form ✓
```

### Scenario B: Past Date Entry
```
1. Click Start Time
2. Click month arrow to go back
3. Click date from previous month
4. Set time
5. Submit form ✓
```

### Scenario C: Future Date Entry
```
1. Click Start Time
2. Click month arrow to go forward
3. Click date from next month
4. Set time
5. Submit form ✓
```

### Scenario D: Manual Entry
```
1. Click Start Time
2. Type: 20/10/2026 09:00
3. Press Tab
4. Value accepted ✓
5. Submit form ✓
```

### Scenario E: Different Time
```
1. Click Start Time
2. Click date 15
3. Change time to 23:59
4. Field shows: 15/10/2026 23:59 ✓
5. Submit form ✓
```

---

## Expected Times

| Action | Expected Time |
|--------|----------------|
| Click field → Calendar appears | <200ms |
| Select date → Field updated | Instant |
| Select time → Field updated | Instant |
| Flatpickr library loads | <1s |
| All assets loaded | <3s |
| Calendar closes | Instant |

---

## Success = ✅

The test is **SUCCESSFUL** if:

1. ✅ Calendar popup appears when clicking Start Time
2. ✅ Calendar shows current month
3. ✅ Can click to select date
4. ✅ Time picker available in calendar
5. ✅ Can set hours and minutes
6. ✅ Format shows: `DD/MM/YYYY HH:mm`
7. ✅ Calendar closes after selection
8. ✅ Form submits without errors
9. ✅ DevTools console has no red errors
10. ✅ Backend receives: `YYYY-MM-DD HH:mm`

---

## Failure = ❌

The test **FAILED** if:

1. ❌ Calendar doesn't appear at all
2. ❌ Calendar appears but dates not clickable
3. ❌ Format wrong (e.g., `MM/DD/YYYY`)
4. ❌ Console shows Flatpickr errors
5. ❌ Network tab shows 404 for flatpickr files
6. ❌ Form fails to submit
7. ❌ Backend receives wrong date format
8. ❌ Only works in certain browsers
9. ❌ Calendar hidden behind other elements
10. ❌ Time picker not available

---

## Questions to Answer

After testing, answer these:

1. **Did the calendar popup appear?**
   - [ ] Yes ✅
   - [ ] No ❌

2. **Could you select a date?**
   - [ ] Yes ✅
   - [ ] No ❌

3. **Could you set the time?**
   - [ ] Yes ✅
   - [ ] No ❌

4. **Was the format correct (DD/MM/YYYY HH:mm)?**
   - [ ] Yes ✅
   - [ ] No, was: ____

5. **Were there console errors?**
   - [ ] No ✅
   - [ ] Yes, saw: ____

6. **Did the form submit?**
   - [ ] Yes ✅
   - [ ] No, got error: ____

7. **Did it work in multiple browsers?**
   - [ ] Yes ✅
   - [ ] Only: ____

8. **Any issues to report?**
   - [ ] None ✅
   - [ ] Yes: ____

---

## Report Template

```
FLATPICKR CALENDAR PICKER TEST RESULTS
Date: _______________
Tester: _______________
Browser: _______________
URL: https://nountutor.com.ng/admin/courses/3/lessons/create

RESULTS:
Calendar appears: ☐ YES ☐ NO
Date selection works: ☐ YES ☐ NO
Time selection works: ☐ YES ☐ NO
Format correct: ☐ YES ☐ NO
Console clean: ☐ YES ☐ NO
Form submits: ☐ YES ☐ NO

OVERALL: ☐ PASS ☐ FAIL

Issues/Notes:
_______________________________________________
_______________________________________________

Screenshots attached: ☐ YES ☐ NO
```

---

## Reference Files

- **Detailed Tests:** `FLATPICKR_TEST_EXECUTION_GUIDE.md`
- **Implementation Details:** `FLATPICKR_IMPLEMENTATION_REPORT.md`
- **Step-by-Step Guide:** `FLATPICKR_TEST_INSTRUCTIONS.md`
- **Full Report:** `FLATPICKR_CALENDAR_PICKER_FINAL_REPORT.md`

---

**Status:** ✅ READY TO TEST  
**Last Updated:** 2026-10-12  
**Next Step:** Run tests and report results
