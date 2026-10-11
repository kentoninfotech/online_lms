# Quick Reference - DateTime Format Testing

## 🎯 Quick Facts
- **Format**: DD/MM/YYYY HH:mm
- **Example**: 16/10/2026 15:00
- **Separator**: / (forward slash, NOT -)
- **Input Type**: Text (not datetime-local)
- **Validation**: Backend checks format strictly
- **Duration**: Auto-calculates when both times filled

---

## 🔑 Key URLs to Test

| Action | URL |
|--------|-----|
| Create Lesson | https://nountutor.com.ng/admin/courses/[ID]/lessons/create |
| Edit Lesson | https://nountutor.com.ng/admin/courses/[ID]/lessons/[ID]/edit |
| View Lesson | https://nountutor.com.ng/admin/courses/[ID]/lessons/[ID] |
| Create Live Session | https://nountutor.com.ng/admin/courses/[ID]/live-sessions/create |
| Edit Live Session | https://nountutor.com.ng/admin/courses/[ID]/live-sessions/[ID]/edit |
| Course Calendar | https://nountutor.com.ng/admin/courses/[ID]/lessons/calendar |

---

## 📝 Test Data

### Lesson Creation
| Field | Test Value |
|-------|-----------|
| Topic | Test Lesson - DD/MM/YYYY Format |
| Start Time | **16/10/2026 15:00** ← Use this format! |
| End Time | **16/10/2026 16:00** ← Use this format! |
| Instructor | Any instructor |
| Capacity | 30 |

### Live Session Creation
| Field | Test Value |
|-------|-----------|
| Session Title | Test Live Session |
| Start Date & Time | **16/10/2026 15:00** ← Use this format! |
| End Date & Time | **16/10/2026 16:00** ← Use this format! |
| Facilitator | Any facilitator |
| Session Type | Jitsi Meet |

---

## ✅ Quick Checklist

### Before Testing
- [ ] You have admin account credentials
- [ ] You can access https://nountutor.com.ng/login
- [ ] You have course IDs ready to test
- [ ] Browser is updated and cache cleared

### During Testing
- [ ] Enter dates in DD/MM/YYYY HH:mm format
- [ ] Verify forms accept input without errors
- [ ] Check that existing dates display in correct format
- [ ] Verify duration auto-calculates (60 min for 15:00-16:00)
- [ ] Verify form validation rejects wrong formats
- [ ] Save forms and verify changes persist

### After Testing
- [ ] All lessons and sessions created successfully
- [ ] Calendar displays correct format
- [ ] Published lessons show proper datetime
- [ ] No validation errors for correct format
- [ ] Documentation updated if needed

---

## 🚨 Common Issues & Solutions

### Issue: Form rejects "16/10/2026 15:00"
**Solution**: 
- Clear browser cache (Ctrl+Shift+R)
- Ensure exact format: DD/MM/YYYY HH:mm
- No extra spaces

### Issue: Duration doesn't auto-calculate
**Solution**:
- Fill BOTH start and end times
- Use correct format: DD/MM/YYYY HH:mm
- Press Tab or click outside field to trigger

### Issue: Old datetime format shows
**Solution**:
- Hard refresh browser (Ctrl+Shift+R)
- Check database has datetime values
- Verify Carbon format is correct: ->format('d/m/Y H:i')

### Issue: Time shows different from what I entered
**Solution**:
- Might be timezone conversion
- Check Africa/Lagos timezone setting
- Times stored as UTC, displayed in user timezone

---

## 📊 Format Validation

### ✅ VALID - These work
```
16/10/2026 15:00
01/01/2027 08:30
31/12/2025 23:59
16/10/2026 00:00
```

### ❌ INVALID - These don't work
```
16-10-2026 15:00  (wrong separator)
2026-10-16 15:00  (wrong order)
16/10/26 15:00    (year too short)
16/10/2026 25:00  (hour invalid)
16/10/2026 15:60  (minute invalid)
```

---

## 💻 Files Changed

### Backend (Controllers)
- `app/Http/Controllers/CourseLessonController.php`
- `app/Http/Controllers/LiveSessionController.php`

### Frontend (Views)
- `resources/views/admin/course-lessons/create.blade.php`
- `resources/views/admin/course-lessons/edit.blade.php`
- `resources/views/admin/live-sessions/create.blade.php`
- `resources/views/admin/live-sessions/edit.blade.php`
- `resources/views/dashboard/add-lesson.blade.php` (verified)
- `resources/views/dashboard/edit-lesson.blade.php` (verified)

---

## 🎯 Success Criteria

You'll know it's working when:

✅ Form placeholder shows: "DD/MM/YYYY HH:mm"  
✅ You can enter: "16/10/2026 15:00"  
✅ Duration auto-fills when you enter both times  
✅ Form submits without validation errors  
✅ Existing datetimes display in DD/MM/YYYY HH:mm format  
✅ Calendar shows events with correct format  
✅ Wrong format shows error: "must match the format d/m/Y H:i"  
✅ Published lessons display correct times  

---

## 📞 Need Help?

1. **Validation Error**: Check format is exactly DD/MM/YYYY HH:mm
2. **Format Not Display**: Refresh browser (Ctrl+Shift+R)
3. **Duration Not Calc**: Ensure both times filled in correct format
4. **Old Format Shows**: Check database update, clear cache
5. **Timezone Issue**: Verify Africa/Lagos timezone in settings

---

## 🚀 Deployment Status

| Component | Status |
|-----------|--------|
| Backend Validation | ✅ Updated |
| Frontend Forms | ✅ Updated |
| JavaScript Parsing | ✅ Added |
| Documentation | ✅ Created |
| Database Changes | ✅ Not needed |
| Testing Guide | ✅ Created |
| Ready to Deploy | ✅ YES |

---

**Last Updated**: 16 October 2026  
**Format Version**: 1.0 (DD/MM/YYYY HH:mm)  
**Status**: ✅ Ready for Testing
