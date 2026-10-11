# Visual Flowchart: Jitsi Room "AIVibeCoding112"

## 🎯 Complete Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                    JITSI MEETING SETUP & FLOW                       │
└─────────────────────────────────────────────────────────────────────┘

STEP 1: ADMIN/INSTRUCTOR CREATES SESSION
────────────────────────────────────────────

Admin Dashboard
    ↓
[Courses] → [Physics 101]
    ↓
[Live Sessions] → [Create]
    ↓
┌────────────────────────────────────────┐
│ Create Live Session Form               │
├────────────────────────────────────────┤
│ Title: Physics Advanced Topics         │
│ Facilitator: Dr. Sarah                 │
│ Start: 16/10/2026 15:00                │
│ End: 16/10/2026 16:00                  │
│ Session Type: Jitsi Meet               │
│ Jitsi Room Name: AIVibeCoding112  ← ⭐│
│ Chat Enabled: ☑️                       │
│                                        │
│ [Schedule Session] ← Click this        │
└────────────────────────────────────────┘
    ↓
Database: INSERT INTO course_live_sessions
    └─ jitsi_room_name = 'AIVibeCoding112' ✅
    ↓
Meeting URL Generated: https://meet.jitsi/AIVibeCoding112 ✅


STEP 2: SESSION APPEARS ON DASHBOARD
─────────────────────────────────────

Student Dashboard
    ↓
[My Courses] → [Physics 101]
    ↓
[Live Sessions]
    ↓
    ┌─────────────────────────────────────────┐
    │ Physics Advanced Topics                 │
    │ [🔴 LIVE NOW]  [3 participants]        │
    │                                         │
    │ [JOIN SESSION] ← Click to access        │
    └─────────────────────────────────────────┘


STEP 3: STUDENT CLICKS & JITSI LOADS
──────────────────────────────────────

Student Clicks [JOIN SESSION]
    ↓
Browser loads: /courses/3/live-session/show
    ↓
Server retrieves:
    └─ SELECT * FROM course_live_sessions WHERE id = 123
    └─ Returns: jitsi_room_name = 'AIVibeCoding112'
    ↓
Blade View (live-session.blade.php) renders:
    ↓
┌────────────────────────────────────────────────┐
│ JavaScript loads Jitsi API:                    │
│                                                │
│ var domain = "meet.jitsi";                    │
│ var options = {                               │
│   roomName: "AIVibeCoding112" ← From DB       │
│   userInfo: {                                 │
│     displayName: "Student Name"               │
│   }                                           │
│ };                                            │
│                                               │
│ var api = new JitsiMeetExternalAPI(           │
│   domain,                                     │
│   options                                     │
│ );                                            │
└────────────────────────────────────────────────┘
    ↓
Jitsi External API loads:
    └─ From: https://meet.jitsi/external_api.js
    ↓
Jitsi Iframe created with URL:
    └─ https://meet.jitsi/AIVibeCoding112
    ↓
Browser Permission Prompt:
    ┌──────────────────────────────────────┐
    │ Allow camera & microphone access?    │
    │ ☐ Remember for this site             │
    │ [Block]     [Allow]  ← Student clicks│
    └──────────────────────────────────────┘
    ↓
Jitsi Embedded in Page:
    ┌──────────────────────────────────────┐
    │  AIVibeCoding112 - Jitsi Meet        │
    ├──────────────────────────────────────┤
    │                                      │
    │  [Instructor Video]  [You]           │
    │                                      │
    │  [Student 2]         [Student 3]     │
    │                                      │
    ├──────────────────────────────────────┤
    │ [🎤] [📷] [🖥️] [💬] [✋] [🔴]        │
    └──────────────────────────────────────┘
    ↓
✅ MEETING STARTED! Students can see instructor


STEP 4: ATTENDANCE TRACKED
──────────────────────────

JavaScript Event Listeners:
    ↓
api.addEventListener('videoConferenceJoined', function() {
    ↓
    Send AJAX: /api/sessions/123/attendance/joined
    ↓
    Database: INSERT INTO live_session_attendances
    └─ live_session_id: 123
    └─ user_id: 45 (Student)
    └─ joined_at: 2026-10-16 15:02:15
    └─ status: 'present'
    ↓
    Attendance Recorded ✅
});


STEP 5: CLASS ENDS
──────────────────

At 16:00 (end time):
    ↓
Facilitator: "Alright everyone, see you next time!"
    ↓
Students Leave Meeting
    ↓
api.addEventListener('videoConferenceLeft', function() {
    ↓
    Update: SET status = 'completed'
    └─ left_at: 2026-10-16 15:47:30
    └─ duration: 45 minutes
    ↓
    Mark Attendance as Completed ✅
});
    ↓
Admin updates status:
    └─ UPDATE course_live_sessions
    └─ SET status = 'completed'
    └─ WHERE id = 123
    ↓
Session Archived ✅
```

---

## 📊 URL Generation Flowchart

```
USER INPUT: Jitsi Room Name
    ↓
    "AIVibeCoding112"
    ↓
SERVER PROCESSING
    ↓
    ┌──────────────────────────────────────┐
    │ if (empty($room_name)) {             │
    │   $room_name = 'room-' .            │
    │     $courseId .                      │
    │     '-' . time();                    │
    │ }                                    │
    │ // Result: room-3-1728663847         │
    └──────────────────────────────────────┘
    ↓
ROOM NAME STORED IN DATABASE
    ↓
    Database: jitsi_room_name = 'AIVibeCoding112'
    ↓
DATABASE TO BLADE VIEW
    ↓
    $session->jitsi_room_name = 'AIVibeCoding112'
    ↓
BLADE TEMPLATE GENERATES JAVASCRIPT
    ↓
    roomName: "{{ $session->jitsi_room_name }}"
    roomName: "AIVibeCoding112"
    ↓
JAVASCRIPT CREATES JITSI URL
    ↓
    domain = "meet.jitsi"
    roomName = "AIVibeCoding112"
    ↓
    URL = domain + "/" + roomName
    URL = "meet.jitsi" + "/" + "AIVibeCoding112"
    URL = "https://meet.jitsi/AIVibeCoding112"
    ↓
✅ FINAL MEETING URL READY
    ↓
    https://meet.jitsi/AIVibeCoding112
```

---

## 🔄 Timeline Visualization

```
TIME        ADMIN              STUDENTS           DATABASE
────────────────────────────────────────────────────────────

14:45       Create             [Dashboard]        [Insert
            Session            (no session yet)    session]
            ↓                  ↓                  ↓
            Fills form         N/A                JITSI_ROOM_NAME
            ✓ AIVibeCoding112                     = 'AIVibeCoding112'
            
14:55       Clicks             [Refreshing]       [Query
            "Schedule"         (checking for      session]
            ↓                  live sessions)     ↓
            ✅ Created          ↓                  
                                Still not live
                                (5 min to go)
            
15:00       Loads session      [Dashboard
            page               shows LIVE]        [Status
            ↓                  ↓                  = scheduled]
            Jitsi loads        Students see
            ✅ Video ready     [LIVE NOW badge]
                               ↓
                               Click to join
                               
15:02       Teaching           Jitsi loads        [Insert
            ✓ Audio on         ↓                  attendance
            ✓ Video on         Browser asks
            ✓ Sharing          permission
                               ↓
                               [Allow]
                               ↓
                               Video loads ✅
                               
15:50       "Class Over"       Leaving            [Update
            ↓                  meeting            attendance]
            Wraps up           ↓
                               Saying goodbye
                               
15:55       Updates Status     Dashboard          [Status
            to "Completed"     shows: Completed   = completed]
            ✓ Done             ✓ Attended         ✓ Recorded
                               ✓ 45 min duration
```

---

## 🎬 What Happens in Jitsi Room

```
JITSI ROOM: AIVibeCoding112
═════════════════════════════════════════════

PARTICIPANT 1: Dr. Sarah (Facilitator)
├─ Joined at: 14:55 (5 min before)
├─ Video: ON ✓
├─ Audio: ON ✓
├─ Screen Share: READY
├─ Recording: READY
├─ Status: Ready to teach

PARTICIPANT 2: Student 1
├─ Joined at: 15:02
├─ Video: ON ✓
├─ Audio: MUTED (listening)
├─ Can: Raise hand, chat, unmute
├─ Status: Listening to lecture

PARTICIPANT 3: Student 2
├─ Joined at: 15:03
├─ Video: OFF (no camera)
├─ Audio: ON ✓
├─ Can: Chat, raise hand, enable camera
├─ Status: Audio only (connectivity issue)

PARTICIPANT 4: Student 3
├─ Joined at: 15:04
├─ Video: ON ✓
├─ Audio: ON ✓
├─ Screen Share: ON (asking for help)
├─ Status: Sharing problem for discussion

FEATURES ENABLED:
├─ Chat: ✓ (enabled, 12 messages sent)
├─ Screen Share: ✓ (instructor + students)
├─ Recording: ✓ (session being recorded)
├─ Hand Raise: ✓ (2 students have hands up)
├─ Emoticons: ✓ (thumbs up, applause)
└─ Stats: ✓ (all connections good)

BANDWIDTH USAGE:
├─ Instructor: 2.5 Mbps (HD video)
├─ Students: 1-2 Mbps each (SD video)
└─ Total: ~7 Mbps (excellent quality)

MEETING DURATION: 45 minutes
└─ Start: 15:02, End: 15:47
└─ Attendance: 100% (3/3 students present)
```

---

## 🔗 Network Flow

```
BROWSER                JITSI SERVER              DATABASE
  ↓                        ↓                         ↓
  └─ HTTP Request ─→ meet.jitsi/
                     AIVibeCoding112
                           ↓
                     ✅ Room exists?
                           ↓
                     ← JSON Response ─→
                           
  ├─ Load Jitsi UI
  ├─ Request camera access
  ├─ Establish WebRTC connection
                           ↓
         ┌──────────────────────────────┐
         │  Peer-to-Peer Video Stream   │
         │  (Direct between browsers)   │
         │  (NOT through server)        │
         │  = Fast & Low Latency! ✓     │
         └──────────────────────────────┘
                           ↓
  ├─ Show video feeds
  ├─ Enable mic/speaker
  ├─ Send chat messages ─→ Chat Server
                                ↓
                           ← Store in DB ←
                           
  └─ Attendance tracking ─→ LMS API
                                ↓
                           INSERT INTO
                           live_session_attendances ←
```

---

## 📱 Multi-Device Access

```
SAME JITSI ROOM: AIVibeCoding112

DEVICE 1: Desktop Browser (Chrome)
├─ Full features
├─ Screen sharing
├─ HD quality
└─ Connected ✓

DEVICE 2: Tablet Browser (Safari)
├─ Most features
├─ Limited screen share
├─ Good quality
└─ Connected ✓

DEVICE 3: Mobile Browser (Firefox)
├─ Video/audio
├─ Chat only
├─ Lower quality
└─ Connected ✓

RESULT:
└─ All 3 devices show same room
└─ All can communicate
└─ Same attendance recorded once
└─ Works across platforms! ✓
```

---

## 🚀 Success Criteria Checklist

```
✅ Room name set: AIVibeCoding112
✅ URL generated: https://meet.jitsi/AIVibeCoding112
✅ Database stored: course_live_sessions.jitsi_room_name
✅ Blade template renders: roomName in JavaScript
✅ Jitsi loads on session page
✅ Students see [LIVE NOW] badge
✅ Browser requests permissions
✅ Students click [Allow]
✅ Jitsi video/audio works
✅ Attendance auto-tracked
✅ Chat enabled and working
✅ Recording available
✅ Session marked completed after
✅ Attendance records saved

→ ALL GREEN = MEETING READY! 🎉
```

---

**Visual Flowchart Complete!**

For detailed steps, see: **JITSI_ROOM_AIVIBECODING112_GUIDE.md**
For quick reference, see: **JITSI_QUICK_REFERENCE.md**
