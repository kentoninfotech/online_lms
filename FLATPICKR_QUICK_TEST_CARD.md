# ⚡ Flatpickr Calendar Picker - Quick Start Testing Guide

## 🎯 30-Second Test

```
1. Go to: https://nountutor.com.ng/admin/courses/3/lessons/create
2. Login: lmsadmin@nountutor.com.ng / @@GLMSAdmin22
3. Click: "Start Time" input field
4. ✅ EXPECT: Calendar popup appears with date/time picker
5. ✅ SELECT: Click on a date (e.g., today)
6. ✅ VERIFY: Field shows "DD/MM/YYYY HH:mm" format
```

---

## 🧪 Test Files Available

| File | Purpose | How to Use |
|------|---------|-----------|
| `FLATPICKR_STANDALONE_TEST.html` | Isolated testing (no server needed) | Open in browser, click "Run All Tests" |
| `FLATPICKR_TEST_AUTOMATION.md` | Detailed step-by-step guide | Read and follow each section |
| `FLATPICKR_TEST_EXECUTION_REPORT.md` | Complete reference (this guide) | Use for detailed debugging |

---

## ✅ Success Indicators

- **✅ Calendar appears** when clicking Start Time field
- **✅ Format is correct**: `DD/MM/YYYY HH:mm` (e.g., `16/10/2026 15:00`)
- **✅ No console errors** (check DevTools: F12 → Console)
- **✅ Form submits** without validation errors

---

## ❌ If Something's Wrong

### Calendar doesn't appear?
1. **Check DevTools** (Press F12)
2. **Go to Console tab**
3. **Run**: `typeof flatpickr` 
   - Returns `"function"` ✅ Library loaded
   - Returns `"undefined"` ❌ CDN failed to load
4. **Check for red errors** in console
5. **Try clearing cache**: Ctrl+Shift+Delete, then reload

### Wrong date format shown?
- Should be: `DD/MM/YYYY HH:mm`
- Wrong example: `2026-10-16` or `10/16/2026` 
- Check `FLATPICKR_TEST_AUTOMATION.md` section "Developer Console Verification"

### Form won't submit?
1. Check validation errors (red text under fields)
2. Verify date format is exactly `DD/MM/YYYY HH:mm`
3. Try manual text entry: Type `16/10/2026 15:00`

---

## 🛠️ Debugging Commands

Run these in DevTools Console (F12 → Console tab):

```javascript
// Check if Flatpickr loaded
typeof flatpickr                    // Should return: "function"

// Check if input found and initialized
document.getElementById('start_time_picker').hasAttribute('data-flatpickr-initialized')
                                            // Should return: true

// Check calendar z-index
const cal = document.querySelector('.flatpickr-calendar');
if (cal) window.getComputedStyle(cal).zIndex;  // Should be high number

// List all datetime inputs
document.querySelectorAll('input[type="datetime-local"]').forEach(e => {
    console.log(e.id + ':', e.hasAttribute('data-flatpickr-initialized'));
});
```

---

## 📋 Test Checklist

- [ ] Can open lesson creation page
- [ ] Can log in successfully
- [ ] Can find "Start Time" field
- [ ] Clicking field opens calendar popup
- [ ] Calendar shows current month
- [ ] Can click on dates in calendar
- [ ] Selected date appears in field
- [ ] Date format is DD/MM/YYYY HH:mm
- [ ] No red errors in DevTools Console
- [ ] Form can be submitted
- [ ] Lesson created successfully

---

## 🌐 Cross-Browser Test Quick Map

| Browser | Expected Result | Special Notes |
|---------|-----------------|---------------|
| Chrome | ✅ Calendar works | Best supported |
| Firefox | ✅ Calendar works | Works well |
| Safari | ✅ Calendar works | May show native picker |
| Edge | ✅ Calendar works | Same as Chrome |
| Mobile Safari | ⚠️ Native picker | Uses system date picker |
| Android Chrome | ⚠️ Native picker | Uses system date picker |

---

## 📸 Screenshots to Capture

1. **Calendar popup open**: Shows date grid and time selector
2. **Date selected**: Shows selected date in field (DD/MM/YYYY HH:mm format)
3. **Console output**: Shows `typeof flatpickr = "function"` and no errors
4. **Form success**: Shows lesson created or success message

---

## 🔑 Key Information

- **Format**: DD/MM/YYYY HH:mm
- **Example**: 16/10/2026 15:00
- **Timezone**: Africa/Lagos (UTC+1)
- **Library**: Flatpickr v4.6.13
- **Fallback**: Can type manually if calendar doesn't work

---

## 🎓 What Each Test Does

### Test 1: Is the library loaded?
- Checks if Flatpickr JavaScript file loaded from CDN
- DevTools command: `typeof flatpickr`
- Should return: `"function"`

### Test 2: Did the input get initialized?
- Checks if Flatpickr found and modified the input field
- DevTools command: `document.getElementById('start_time_picker').hasAttribute('data-flatpickr-initialized')`
- Should return: `true`

### Test 3: Does the calendar appear?
- Manually click on Start Time field
- Wait for calendar popup to appear
- Calendar should be visible, not hidden behind other elements

### Test 4: Can you select a date?
- Click on a date in the calendar
- Check if selected date appears in the input field
- Verify format is DD/MM/YYYY HH:mm

### Test 5: Can you submit the form?
- Fill in all required fields
- Click "Create Lesson"
- Watch for errors or success message
- Verify lesson was created in system

---

## 🚀 Expected Behavior Timeline

```
1. Page Load (0-2 seconds)
   → Flatpickr library loads from CDN
   → Initialization script runs

2. User Interaction (when clicking field)
   → Click on Start Time input
   → Calendar popup appears below field
   → Calendar shows current date highlighted

3. Date Selection (user picks date)
   → Click on date in calendar
   → Calendar closes
   → Field displays selected date as DD/MM/YYYY HH:mm

4. Form Submission (when clicking Create)
   → All fields validated
   → Datetime format checked: d/m/Y H:i
   → Server receives: DD/MM/YYYY HH:mm
   → Lesson created in database
```

---

## 💡 Pro Tips

1. **Hard Refresh**: If changes don't show, press **Ctrl+F5** (or Cmd+Shift+R on Mac)
2. **Clear Cache**: If still not working, clear browser cache (Ctrl+Shift+Delete)
3. **Try Different Browser**: If one browser fails, try Chrome/Firefox/Safari
4. **Check Network**: In DevTools → Network tab, verify CDN requests succeed (Status 200)
5. **Copy Exact Error**: If you see an error, copy the full message to share

---

## 📞 What to Report

If the test fails, provide:

1. **Browser**: (Chrome, Firefox, Safari, etc.)
2. **Version**: (e.g., Chrome v126)
3. **What you expected**: (Calendar should open)
4. **What happened instead**: (Nothing happened / Error appeared)
5. **DevTools Console**: `typeof flatpickr` result
6. **Screenshots**: Of the problem
7. **Steps to reproduce**: Exact sequence to see the issue

---

## ✨ Success Confirmation

When everything works, you'll see:

```
✅ Flatpickr library loaded
✅ Input field recognized and initialized
✅ Calendar popup appears on click
✅ Date format shows as DD/MM/YYYY HH:mm
✅ Form submits without errors
✅ Lesson created successfully
```

If you see all ✅ marks, the feature is **WORKING** 🎉

---

**Version**: 1.0 | **Updated**: 12/10/2026 | **Status**: Ready to Test
