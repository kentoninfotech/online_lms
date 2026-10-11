# Jitsi Meeting Setup & Access - Complete Answer

## Your Question: Room Name "AIVibeCoding112"

### The Meeting Link URL
```
https://meet.jitsi/AIVibeCoding112
```

---

## 🎯 How to Start the Meeting

### Step 1: Create the Live Session (Admin/Instructor)

**Navigate to:**
```
Admin → Courses → [Your Course] → Live Sessions → Create
```

**Fill in the form:**
```
┌─────────────────────────────────────────┐
│ Session Title                           │
│ ├─ "Physics Advanced Topics - Lecture 5"│
│ └─────────────────────────────────────── │
│ Facilitator                             │
│ ├─ Select your name from dropdown       │
│ └─────────────────────────────────────── │
│ Start Date & Time                       │
│ ├─ 16/10/2026 15:00 (3:00 PM)          │
│ └─────────────────────────────────────── │
│ End Date & Time                         │
│ ├─ 16/10/2026 16:00 (4:00 PM)          │
│ └─────────────────────────────────────── │
│ Session Type                            │
│ ├─ "Jitsi Meet" (selected)              │
│ └─────────────────────────────────────── │
│ Jitsi Room Name                         │
│ ├─ "AIVibeCoding112" ← YOUR CUSTOM NAME │
│ └─────────────────────────────────────── │
│ Chat Enabled                            │
│ ├─ ☑️ (checked for student interaction) │
│ └─────────────────────────────────────── │
│ [Schedule Session] ← CLICK THIS          │
└─────────────────────────────────────────┘
```

**Result:**
✅ Session created successfully  
✅ Stored in database with room name "AIVibeCoding112"  
✅ URL automatically becomes: `https://meet.jitsi/AIVibeCoding112`

---

### Step 2: Notify Students

Send announcement (email/SMS/dashboard):
```
📢 LIVE SESSION STARTING!

Class: Physics Advanced Topics
Time: 16/10/2026 at 3:00 PM (Nigeria Time)
Duration: 1 hour
Facilitator: Dr. Sarah Johnson

Join here: https://nountutor.com.ng/courses/3/live-session/show
(Meeting room: AIVibeCoding112)
```

---

### Step 3: The Meeting Starts Automatically

**When students access the session:**

```
Timeline:
14:55 - Admin joins early to test
        └─ Page loads with Jitsi embedded
        
15:00 - [LIVE NOW] badge appears on dashboard
        └─ Students see: "LIVE NOW - Physics Advanced Topics"
        
15:02 - First student clicks session
        └─ Browser says: "Allow camera/microphone?"
        └─ Student clicks "Allow"
        └─ Jitsi loads automatically
        └─ They can see the admin/facilitator
        
15:05 - Class fully loaded
        └─ Everyone visible and audio working
        └─ Teaching can begin! 🎓
```

---

## 📱 Student Experience: How They Access It

### What Student Sees on Dashboard
```
┌──────────────────────────────────────┐
│ COURSES → MY COURSES                 │
├──────────────────────────────────────┤
│                                      │
│ Physics 101                          │
│ │                                    │
│ └─ Live Sessions                     │
│    │                                 │
│    └─ Physics Advanced Topics        │
│       [LIVE NOW 🔴] [3 participants] │
│       "Join the live class now"      │
│                                      │
└──────────────────────────────────────┘
```

### What Happens When They Click "Join"
```
1. Page loads with heading:
   "Physics Advanced Topics - LIVE SESSION"
   
2. Jitsi Meet automatically embeds:
   ┌─────────────────────────────────┐
   │  AIVibeCoding112 - Jitsi Meet   │
   ├─────────────────────────────────┤
   │                                 │
   │  [Dr. Sarah's Video]   [You]    │
   │                                 │
   │  [Student 2]         [Student 3]│
   │                                 │
   ├─────────────────────────────────┤
   │ [Mic] [Cam] [Share] [Chat] ...  │
   └─────────────────────────────────┘
   
3. Browser prompts:
   "Allow nountutor.com to access camera/microphone?"
   └─ Student clicks [Allow]
   
4. Meeting starts! 🎉
   └─ Can see instructor & classmates
   └─ Can talk & share
   └─ Chat enabled
   └─ Can raise hand
```

---

## 🔗 Three Ways to Access the Meeting

### Method 1: Via LMS Dashboard (BEST ⭐⭐⭐)
```
Dashboard → Courses → Physics 101 → Live Sessions → [Session]
```
✅ Automatic attendance tracking
✅ Secure (requires login + enrollment)
✅ Full LMS integration
✅ RECOMMENDED

### Method 2: Direct Jitsi URL
```
https://meet.jitsi/AIVibeCoding112
```
✅ Works anywhere
❌ No attendance tracking
❌ Anyone can join without enrollment
⚠️ Only use if needed

### Method 3: Custom Domain (If Self-Hosted)
```
https://your-jitsi-server.com/AIVibeCoding112
```
✅ For enterprise setups
✅ Full control
❌ Requires self-hosted Jitsi server

---

## 🎬 Meeting Features Available

Once inside `AIVibeCoding112` meeting:

| Feature | Button | Purpose | Who |
|---------|--------|---------|-----|
| **Microphone** | 🎤 | Mute/unmute audio | All |
| **Camera** | 📷 | Show/hide video | All |
| **Screen Share** | 🖥️ | Share slides/screen | Facilitator+ |
| **Chat** | 💬 | Send text messages | All |
| **Raise Hand** | ✋ | Signal to ask question | Students |
| **Recording** | 🔴 | Save the session | Facilitator |
| **Stats** | 📊 | Connection quality | All |
| **Fullscreen** | ⛶ | Maximize view | All |

---

## 🔐 URL Generation Formula

**Your Room Name:** `AIVibeCoding112`

```
URL = https://meet.jitsi/ + {JITSI_ROOM_NAME}
    = https://meet.jitsi/ + AIVibeCoding112
    = https://meet.jitsi/AIVibeCoding112
```

### Other Examples:

| Room Name | Generated URL |
|-----------|---------------|
| `Biology-Lab-01` | `https://meet.jitsi/Biology-Lab-01` |
| `lesson-english-nov2026` | `https://meet.jitsi/lesson-english-nov2026` |
| `room-123-567890` | `https://meet.jitsi/room-123-567890` |
| `AutoGenerated` | `https://meet.jitsi/AutoGenerated` |

### Auto-Generation (If Left Blank)
```
If Jitsi Room Name = [EMPTY]
Then system generates: room-{COURSE_ID}-{TIMESTAMP}
Example: room-3-1728663847
URL: https://meet.jitsi/room-3-1728663847
```

---

## 📊 Database Storage

Your session is stored as:

```sql
INSERT INTO course_live_sessions 
(course_id, facilitator_id, title, jitsi_room_name, 
 scheduled_start, scheduled_end, session_type, status)
VALUES 
(3, 7, 'Physics Advanced Topics', 'AIVibeCoding112',
 '2026-10-16 15:00:00', '2026-10-16 16:00:00', 'jitsi', 'scheduled');

-- Retrieved as:
SELECT * FROM course_live_sessions 
WHERE jitsi_room_name = 'AIVibeCoding112';
```

---

## ⏰ Complete Timeline Example

```
🕐 14:45 - YOU (Facilitator)
         └─ Go to: Admin → Courses → Physics 101 → Live Sessions → Create
         └─ Fill form with: Room Name = "AIVibeCoding112"
         └─ Click "Schedule Session"
         └─ System stores in database ✅

🕐 14:55 - YOU
         └─ (5 min before class)
         └─ Open: https://nountutor.com.ng/courses/3/live-session/show
         └─ Jitsi loads: https://meet.jitsi/AIVibeCoding112
         └─ Test camera/microphone
         └─ "Everything works!"

🕐 15:00 - STUDENTS
         └─ Dashboard shows: [LIVE NOW 🔴]
         └─ Get notification: "Class starting now!"
         └─ Click session link
         └─ Jitsi loads for them too
         └─ Browser asks: "Allow camera?"
         └─ They click "Allow"
         └─ They see you! 👋

🕐 15:02 - EVERYONE
         └─ 12 students joined
         └─ Audio working ✅
         └─ Video working ✅
         └─ Chat enabled ✅
         └─ You: "Good afternoon everyone, let's start!"
         └─ Teaching begins! 🎓

🕐 15:50 - YOU
         └─ "Alright, that's all for today"
         └─ Students leave meeting

🕐 15:55 - YOU
         └─ Leave meeting
         └─ Go to admin panel
         └─ Update session status: "completed"
         └─ Attendance auto-saved in database ✅
         └─ Done! 🎉
```

---

## ✅ Quick Checklist

### Before Creating Session
- [ ] You have admin/instructor role
- [ ] Decide on session title
- [ ] Pick a memorable room name (e.g., AIVibeCoding112)
- [ ] Know exact start & end time
- [ ] Select the right course

### Creating the Session
- [ ] Fill in title
- [ ] Select facilitator (yourself)
- [ ] Set start time (16/10/2026 15:00)
- [ ] Set end time (16/10/2026 16:00)
- [ ] Session Type: "Jitsi Meet"
- [ ] Jitsi Room: "AIVibeCoding112"
- [ ] Chat Enabled: ☑️ (checked)
- [ ] Click "Schedule Session"

### Before Class (15 min early)
- [ ] Test internet connection
- [ ] Open session from dashboard
- [ ] Check Jitsi loads
- [ ] Test camera works
- [ ] Test microphone works
- [ ] Ask test question on chat

### During Class
- [ ] Share your screen if needed
- [ ] Use chat for questions
- [ ] Encourage hand raises
- [ ] Record if needed
- [ ] Monitor attendance

### After Class
- [ ] End meeting
- [ ] Mark session as "completed"
- [ ] Check attendance records
- [ ] Save recording (if made)

---

## 🆘 If Something Goes Wrong

### Jitsi won't load
```
Solution:
1. Refresh the page
2. Clear browser cache (Ctrl+Shift+Delete)
3. Try different browser
4. Check: Is Jitsi Room Name set? (not empty)
```

### No camera/microphone
```
Solution:
1. Check browser notification - click "Allow"
2. Go to browser Settings → Privacy
3. Check: Camera and Microphone are allowed
4. Unplug/replug USB devices
5. Restart browser
```

### Can't find the session
```
Solution:
1. Verify you're enrolled in course
2. Verify session is published
3. Verify session start time has arrived
4. Refresh dashboard
5. Check course is active
```

### Students can't see you
```
Solution:
1. Check your camera is on
2. Check you have good internet
3. Exit and re-enter meeting
4. Restart Jitsi
5. Try different browser
```

---

## 📖 Full Documentation Files

We've created comprehensive guides:

1. **JITSI_QUICK_REFERENCE.md** ← Start here! Quick & simple
2. **JITSI_MEETING_GUIDE.md** ← Complete technical guide
3. **LIVE_SESSION_DATETIME_FIX.md** ← Datetime validation details
4. **COURSE_LESSONS_CALENDAR_FEATURE.md** ← Calendar integration
5. **COURSE_LESSONS_QUICK_START.md** ← Lesson scheduling

---

## 🎯 Summary: Your Meeting Setup

| Item | Value |
|------|-------|
| **Room Name** | AIVibeCoding112 |
| **Meeting URL** | https://meet.jitsi/AIVibeCoding112 |
| **LMS Access** | Dashboard → Sessions → [Click Session] |
| **Setup Time** | 2 minutes |
| **Participants Limit** | 100+ (free) |
| **Start Method** | Automatic when accessed |
| **Attendance** | Auto-tracked via LMS |
| **Features** | Video, audio, chat, screen share, record |
| **Cost** | FREE ✅ |

---

## 🚀 Ready to Launch?

```
✅ Room Name: AIVibeCoding112 is ready
✅ URL: https://meet.jitsi/AIVibeCoding112 works
✅ System: Integrated with LMS dashboard
✅ Students: Can access from dashboard
✅ Tracking: Automatic attendance recording

NEXT STEP: Create your first live session!
```

---

**Questions? Check the guides linked above or contact your admin!** 📞

---

*Last Updated: October 11, 2026*  
*Jitsi Version: Latest (meet.jitsi)*  
*LMS: Course Live Sessions Feature*
