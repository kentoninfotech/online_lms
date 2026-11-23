# ✅ Flatpickr Testing - Pre-Test Checklist

**Date**: 12/10/2026  
**Feature**: Calendar Picker with DateTime Selection  
**Status**: Ready for Testing  

---

## 📋 Before You Start Testing

### System Readiness
- [ ] Internet connection available (CDN needs it)
- [ ] Browser is modern (Chrome, Firefox, Safari, Edge)
- [ ] JavaScript is enabled in browser settings
- [ ] Pop-up blocker is not blocking calendar popups
- [ ] No aggressive browser extensions running
- [ ] Have admin/test login credentials ready

### Browser Setup
- [ ] Browser updated to latest version
- [ ] DevTools available (press F12 to verify)
- [ ] Console tab accessible in DevTools
- [ ] Network tab accessible in DevTools
- [ ] Allow camera/microphone (if prompted for Jitsi rooms)

### Documentation Prepared
- [ ] Have FLATPICKR_TESTING_INDEX.md available
- [ ] Have FLATPICKR_TESTING_SUMMARY.md available
- [ ] Have FLATPICKR_QUICK_TEST_CARD.md available
- [ ] Know where other guides are located
- [ ] Have notepad ready for notes

---

## 🎯 Test Information

### Test Target
- **URL**: https://nountutor.com.ng/admin/courses/3/lessons/create
- **Page**: Lesson Creation Form (Admin Dashboard)
- **Timezone**: Africa/Lagos (UTC+1)
- **Expected Format**: DD/MM/YYYY HH:mm (e.g., 16/10/2026 15:00)

### Credentials
- **Email**: lmsadmin@nountutor.com.ng
- **Password**: @@GLMSAdmin22
- **Role**: Administrator
- **Access**: Lesson creation and management

### Target Input Fields
- **Primary**: "Start Time" datetime picker
- **Secondary**: "End Time" datetime picker (if visible)
- **Format**: DD/MM/YYYY HH:mm with 24-hour time

---

## ✅ Testing Checklist

### Phase 1: Page Load & Login (5 minutes)

**Actions**:
- [ ] Navigate to test URL
- [ ] See login form (if not logged in)
- [ ] Enter email: lmsadmin@nountutor.com.ng
- [ ] Enter password: @@GLMSAdmin22
- [ ] Click Login button
- [ ] Wait for page to load
- [ ] See lesson creation form with input fields

**Verification**:
- [ ] Page loaded successfully
- [ ] No loading errors
- [ ] Form is visible and ready
- [ ] All input fields present
- [ ] "Start Time" field visible with placeholder

**Issues**:
- If page won't load: [ ] Check internet connection
- If login fails: [ ] Verify credentials are correct
- If form not showing: [ ] Refresh page (F5)

---

### Phase 2: Inspect Input Element (2 minutes)

**Actions**:
- [ ] Right-click on "Start Time" input field
- [ ] Select "Inspect" or "Inspect Element"
- [ ] Look at HTML code for the input

**Verification - Look For**:
- [ ] `type="datetime-local"` (NOT type="text")
- [ ] `id="start_time_picker"` (or similar identifier)
- [ ] `placeholder="DD/MM/YYYY HH:mm"`
- [ ] `data-flatpickr-enabled="true"` (or similar marker)

**Screenshot**:
- [ ] Take screenshot of Inspector showing input element
- Save as: "01_Input_Element.png"

**Issues**:
- [ ] If type is "text": Feature not implemented yet
- [ ] If type is "datetime-local": Feature is implemented, continue

---

### Phase 3: Console Verification (2 minutes)

**Actions**:
- [ ] Press F12 to open DevTools
- [ ] Click on "Console" tab
- [ ] Type: `typeof flatpickr`
- [ ] Press Enter
- [ ] Look at the result

**Expected Result**:
- [ ] Console shows: `"function"`

**Screenshot**:
- [ ] Take screenshot of console showing result
- Save as: "02_Console_Flatpickr.png"

**Interpretation**:
- `"function"` = ✅ Library loaded successfully
- `"undefined"` = ❌ Library failed to load from CDN
- Error message = ❌ JavaScript error occurred

**If Result is "undefined"**:
- [ ] Check Network tab (below)
- [ ] Clear browser cache: Ctrl+Shift+Delete
- [ ] Hard refresh page: Ctrl+F5
- [ ] Try again

---

### Phase 4: Network Verification (Optional but Recommended)

**Actions**:
- [ ] Click "Network" tab in DevTools
- [ ] Refresh page: F5
- [ ] Filter by: "flatpickr" in search box
- [ ] Look for these requests:
  - flatpickr.min.css
  - light.css
  - flatpickr.min.js

**Expected Result**:
- [ ] All 3 resources show Status: 200 (success)
- [ ] All resources load from cdn.jsdelivr.net

**Screenshot**:
- [ ] Take screenshot of Network tab
- Save as: "03_Network_Requests.png"

**Interpretation**:
- Status 200 = ✅ Resource loaded successfully
- Status 404/403 = ❌ Resource not found or blocked
- Blocked/CORS error = ❌ Network/proxy blocking CDN

**If Status is Not 200**:
- [ ] Check company firewall/proxy settings
- [ ] CDN might be blocked
- [ ] Try different browser or network
- [ ] Feature may not work if CDN is blocked

---

### Phase 5: Calendar Popup Test (3 minutes)

**Actions**:
1. [ ] Click on the "Start Time" input field
2. [ ] Wait 1-2 seconds for calendar to appear
3. [ ] Look for calendar popup below or near input
4. [ ] Observe calendar appearance

**Expected Result - Calendar Should Show**:
- [ ] Calendar popup appears
- [ ] Month/Year visible at top (e.g., "October 2026")
- [ ] Date grid with clickable dates
- [ ] Navigation arrows (< and >)
- [ ] Time selector if datetime (hours and minutes)
- [ ] Calendar is fully visible (not cut off)

**Screenshot - Calendar Popup**:
- [ ] Take screenshot showing calendar open
- Save as: "04_Calendar_Popup.png"

**Interpretation**:
- Calendar appears = ✅ Feature is working
- Calendar doesn't appear = ❌ Continue to Phase 6 debugging

---

### Phase 6A: Date Selection Test (If Calendar Appeared)

**Actions**:
1. [ ] Click on today's date in calendar (or any visible date)
2. [ ] Observe the selection
3. [ ] Check if calendar closes
4. [ ] Look at the input field for the value

**Expected Result**:
- [ ] Date is highlighted/selected in calendar
- [ ] Calendar closes (or stays open)
- [ ] Input field shows selected date
- [ ] Format is: DD/MM/YYYY HH:mm
- [ ] Example: 16/10/2026 15:00

**Verification**:
- [ ] Format starts with DD/ (day first)
- [ ] Format has slashes: / not hyphens: -
- [ ] Format has HH:mm for time (not just date)

**Screenshot - Date Selected**:
- [ ] Take screenshot of input field with date
- Save as: "05_Date_Selected.png"

**Notes**:
- If time not shown: Might be date-only input (acceptable)
- If format different: Check QUICK_TEST_CARD.md for debugging

---

### Phase 6B: Debugging Test (If Calendar Didn't Appear)

**Actions**:
1. [ ] Close/minimize calendar if open
2. [ ] Click on input field again
3. [ ] Still no calendar? Let's debug:

**Console Debug - Input Initialization**:
```javascript
// Copy and paste this in console:
const input = document.getElementById('start_time_picker');
console.log({
  exists: !!input,
  type: input?.type,
  initialized: input?.hasAttribute('data-flatpickr-initialized')
});
```

**Actions**:
- [ ] Right-click console
- [ ] Select "Copy" → Paste the code above
- [ ] Press Enter
- [ ] Look at the output

**Expected Output**:
- `exists: true`
- `type: "datetime-local"`
- `initialized: true`

**Screenshot**:
- [ ] Take screenshot of console with results
- Save as: "06_Debug_Input_Status.png"

**Interpretation**:
- `exists: true` = Input field found ✅
- `exists: false` = Input not found ❌
- `initialized: true` = Flatpickr initialized ✅
- `initialized: false` = Flatpickr didn't initialize ❌
- `type: "datetime-local"` = Correct type ✅
- `type: "text"` = Wrong type ❌

---

### Phase 7: Form Submission Test (3 minutes)

**Actions**:
1. [ ] Fill in all required form fields:
   - [ ] Select Subject (dropdown)
   - [ ] Select Student (if visible)
   - [ ] Select Start Time (using calendar or manual entry)
   - [ ] Enter Duration (e.g., 60 minutes)
   - [ ] Keep Recurrence as "None"

2. [ ] Click "Create Lesson" button

3. [ ] Wait for form to process (page might refresh)

4. [ ] Observe result

**Expected Result**:
- [ ] Form submits without validation errors
- [ ] No red error messages
- [ ] Either:
  - Page redirects to success page, OR
  - Shows success message, OR
  - Lesson appears in list

**Verification**:
- [ ] No "Date format error" message
- [ ] No "Invalid datetime" error
- [ ] No 422/400 server error

**Screenshot**:
- [ ] Take screenshot of result (success or error)
- Save as: "07_Form_Submission.png"

**Notes**:
- If datetime format error: Format not recognized by backend
- If server error: Check server logs
- If success: Everything working correctly ✅

---

## 🎯 Test Summary

After completing all phases, fill in this summary:

### Overall Results

**Calendar Functionality**:
- [ ] Calendar popup appears: YES / NO
- [ ] Calendar has correct content: YES / NO
- [ ] Date selection works: YES / NO
- [ ] Calendar closes properly: YES / NO

**DateTime Format**:
- [ ] Format shown: ________________
- [ ] Expected format: DD/MM/YYYY HH:mm
- [ ] Format matches: YES / NO

**Form Submission**:
- [ ] Form submitted: YES / NO
- [ ] No validation errors: YES / NO
- [ ] Lesson created: YES / NO

**Console Status**:
- [ ] typeof flatpickr: "function" / "undefined"
- [ ] Red errors visible: YES / NO
- [ ] Initialization successful: YES / NO

### Overall Assessment

- [ ] ✅ **WORKING** - Calendar popup appears, format correct, form submits
- [ ] ⚠️ **PARTIAL** - Calendar appears but some issues (specify below)
- [ ] ❌ **NOT WORKING** - Calendar doesn't appear or form fails

**Specific Issues Found** (if any):
```
1. _________________________________
2. _________________________________
3. _________________________________
```

---

## 📸 Screenshots Captured

- [ ] 01_Input_Element.png - HTML structure of input
- [ ] 02_Console_Flatpickr.png - typeof flatpickr result
- [ ] 03_Network_Requests.png - CDN resource loads
- [ ] 04_Calendar_Popup.png - Calendar popup open
- [ ] 05_Date_Selected.png - Selected date in field
- [ ] 06_Debug_Input_Status.png - Console debug output
- [ ] 07_Form_Submission.png - Form submission result

---

## 📝 Test Notes

Use this space for additional observations:

```
Browser: _______________
OS: _______________
Test Date: _______________
Tester Name: _______________

Observations:
_________________________________
_________________________________
_________________________________
_________________________________

Recommendations:
_________________________________
_________________________________
_________________________________
```

---

## ✅ Test Completion Checklist

### Before Submitting Results
- [ ] All phases completed
- [ ] Screenshots taken and organized
- [ ] Notes documented
- [ ] Overall assessment decided
- [ ] Issues (if any) clearly described
- [ ] DevTools output captured (if debugging)

### Results to Report
- [ ] Overall result (WORKING / PARTIAL / BROKEN)
- [ ] Calendar appearance (YES / NO)
- [ ] Date format (correct / incorrect)
- [ ] Console status (errors / clean)
- [ ] Form submission result (success / failure)

### Optional but Helpful
- [ ] Browser and OS information
- [ ] Screenshots attached
- [ ] Console error messages copied
- [ ] Exact reproduction steps
- [ ] Suggestions for fixes (if applicable)

---

## 🎯 Quick Result Summary

After all testing, quickly summarize:

```
FLATPICKR CALENDAR PICKER - QUICK RESULT
=========================================

Calendar Popup Status: ✅ APPEARS / ❌ DOESN'T APPEAR
Date Format: DD/MM/YYYY HH:mm ✅ CORRECT / ❌ WRONG
Console Errors: ❌ FOUND / ✅ NONE
Form Submission: ✅ SUCCESS / ❌ FAILED

OVERALL: ✅ WORKING / ⚠️ PARTIAL / ❌ BROKEN

Browser: _____________________
Date Tested: _____________________

Any comments: _______________________
```

---

**Checklist Version**: 1.0  
**Last Updated**: 12/10/2026  
**Status**: Ready to Use  

✅ **Print this checklist and use it during testing!**
