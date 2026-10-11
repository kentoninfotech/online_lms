# Jitsi Meeting Access Guide - Live Session Feature

## How the Jitsi Meeting Works

When you set a **Jitsi Room Name** (e.g., `AIVibeCoding112`), the system automatically generates a meeting link and embeds it directly in the student view.

---

## Example: Room Name "AIVibeCoding112"

### The Meeting URL Structure

Based on your Jitsi Room Name: `AIVibeCoding112`

The meeting URL will be:
```
https://meet.jitsi/AIVibeCoding112
```

| Component | Value |
|-----------|-------|
| **Jitsi Domain** | `meet.jitsi` (default, can be configured) |
| **Room Name** | `AIVibeCoding112` |
| **Full URL** | `https://meet.jitsi/AIVibeCoding112` |

---

## How to Access the Meeting

### For Students/Attendees

#### Method 1: Through the LMS Dashboard (Recommended)
1. Log in to the LMS
2. Navigate to: **Courses → {Course Name} → Live Sessions**
3. Click on the active live session
4. **The Jitsi meeting will automatically load** in the "Jitsi Video Conference" section
5. Your browser will ask for camera/microphone permissions
6. Click **"Allow"** to enable audio/video
7. 📹 You're now in the meeting!

#### Method 2: Direct URL Access (Manual)
If needed, you can access the meeting directly at:
```
https://meet.jitsi/AIVibeCoding112
```

However, this bypasses the LMS tracking/attendance system.

---

## How to Start the Meeting

### Step 1: Schedule the Live Session
1. Go to: **Admin → Courses → {Course ID} → Live Sessions → Create**
2. Fill in the form:
   - **Title:** "Physics Advanced Topics - Session 1"
   - **Facilitator:** Select your name
   - **Start Date & Time:** 16/10/2026 15:00
   - **End Date & Time:** 16/10/2026 16:00
   - **Session Type:** Jitsi Meet
   - **Jitsi Room Name:** `AIVibeCoding112` ← Your custom room name
3. Click **"Schedule Session"**

### Step 2: Wait Until Session Time
The session will be visible to students when:
- ✅ Session is published (automatically by default)
- ✅ Session scheduled_start time arrives (or student clicks it before time)

### Step 3: Students Join
Students will see:
```
[LIVE SESSION: Physics Advanced Topics - Session 1]
├─ LIVE NOW badge (when session time arrives)
├─ Online: 0 participants
├─ Video Conference Container (Jitsi loads here)
└─ Facilitator Info
```

### Step 4: Meeting Starts Automatically
When a student accesses the session page, **Jitsi automatically loads** with:
- ✅ Video feed embedded in the LMS
- ✅ Microphone/camera enabled (after permission)
- ✅ Chat enabled (if configured)
- ✅ Screen sharing available
- ✅ Recording option (if admin enables)
- ✅ Hand raise for Q&A
- ✅ Stats/diagnostics available

---

## What Happens Inside the Meeting

### Jitsi Meet Interface

When your room `AIVibeCoding112` opens, you'll see:

```
┌─────────────────────────────────────────────────┐
│  AIVibeCoding112 - Jitsi Meet                   │
├─────────────────────────────────────────────────┤
│                                                   │
│    [Your Video Feed]    [Participant 2]         │
│                                                   │
│    [Participant 3]      [Participant 4]         │
│                                                   │
├─────────────────────────────────────────────────┤
│ [Mic] [Camera] [Quality] [Fullscreen] [Settings]│
│ [Record] [Stats] [Raise Hand] [Chat] [Help]     │
└─────────────────────────────────────────────────┘
```

### Available Features

| Feature | Purpose | Who Can Use |
|---------|---------|-------------|
| **Microphone Toggle** | Mute/unmute audio | All participants |
| **Camera Toggle** | Show/hide video | All participants |
| **Screen Share** | Share your screen/slides | Facilitator & Students |
| **Video Quality** | Adjust resolution (HD/SD) | All participants |
| **Fullscreen** | Maximize meeting view | All participants |
| **Recording** | Record the session (if enabled) | Facilitator only |
| **Stats** | View bandwidth & connection info | All participants |
| **Raise Hand** | Signal to ask a question | Students |
| **Chat** | Send text messages | All participants (if enabled) |
| **Help** | Access Jitsi documentation | All participants |

---

## Configuration Options

### In your `.env` file (Optional - for self-hosted Jitsi)

```env
# Default cloud Jitsi
JITSI_DOMAIN=meet.jitsi
JITSI_SELF_HOSTED=false

# OR for self-hosted Jitsi server
JITSI_DOMAIN=your-domain.com
JITSI_SELF_HOSTED=true
JITSI_SERVER_URL=https://your-jitsi-server.com
```

### In the LMS Database

All settings are stored in `course_live_sessions` table:

```sql
-- Your session
SELECT * FROM course_live_sessions 
WHERE jitsi_room_name = 'AIVibeCoding112';

-- Result columns
┌─────────────────────────────────────────┐
│ id                 │ 5                   │
│ course_id          │ 3                   │
│ facilitator_id     │ 7                   │
│ title              │ Physics Class       │
│ jitsi_room_name    │ AIVibeCoding112     │
│ session_type       │ jitsi               │
│ scheduled_start    │ 2026-10-16 15:00:00 │
│ scheduled_end      │ 2026-10-16 16:00:00 │
│ status             │ scheduled           │
│ chat_enabled       │ 1 (true)            │
│ is_compulsory      │ 1 (true)            │
└─────────────────────────────────────────┘
```

---

## Complete Step-by-Step: Create & Start Meeting

### Admin/Instructor Steps

**Step 1: Create Session (5 min before class)**
```
1. Courses → Physics 101 → Live Sessions → Create
2. Title: "Electromagnetic Waves Lecture"
3. Facilitator: Dr. Sarah
4. Start: 16/10/2026 14:55 (5 min before)
5. End: 16/10/2026 16:25
6. Jitsi Room: AIVibeCoding112
7. Click "Schedule Session"
```

**Step 2: Notify Students** (send announcement)
```
Email/SMS to class:
"Live session starts in 5 minutes! Join at:
https://nountutor.com.ng/courses/3/live-session/show
(Will redirect to: https://meet.jitsi/AIVibeCoding112)"
```

**Step 3: Go Live**
```
1. At 14:55, navigate to the session page
2. Jitsi automatically loads
3. Test camera/microphone
4. Tell students to join
5. Wait for participants
6. Start teaching when ready
```

### Student Steps

**Step 1: See Notification**
```
Email/SMS: "Live session starting now!"
Dashboard shows: [LIVE NOW] badge
```

**Step 2: Join Session**
```
1. Click: Courses → Physics 101 → Live Sessions
2. Click active session
3. Page loads with Jitsi meeting
4. Browser asks: "Allow camera/microphone?"
5. Click "Allow"
6. You're in! (see instructor & classmates)
```

**Step 3: Participate**
```
- Use microphone to ask questions
- Enable camera to be seen
- Raise hand for turn to speak
- Use chat for messages
- Share screen if needed
```

---

## Automatic URL Generation Examples

| Jitsi Room Name | Generated URL |
|-----------------|---------------|
| `AIVibeCoding112` | `https://meet.jitsi/AIVibeCoding112` |
| `Physics-Class-1` | `https://meet.jitsi/Physics-Class-1` |
| `room-123-456789` | `https://meet.jitsi/room-123-456789` |
| `lecture_sept_2026` | `https://meet.jitsi/lecture_sept_2026` |

---

## Auto-Generation (If No Room Name Provided)

If you leave "Jitsi Room Name" **blank** when creating a session:

```php
// System auto-generates:
$room_name = 'room-' . $course_id . '-' . time();
// Example: room-3-1728663847

// URL becomes:
https://meet.jitsi/room-3-1728663847
```

---

## Troubleshooting

### Meeting Won't Load
1. ✅ Check room name is set (not empty)
2. ✅ Verify session is published
3. ✅ Check your internet connection
4. ✅ Try refreshing the page
5. ✅ Check browser console for errors (F12)

### No Sound/Video
1. ✅ Grant permission when browser asks
2. ✅ Check browser privacy settings
3. ✅ Verify camera/microphone in Settings
4. ✅ Try unplugging and re-plugging hardware
5. ✅ Try a different browser

### Can't See Other Participants
1. ✅ Check everyone is connected to the meeting
2. ✅ Verify participants' video is enabled
3. ✅ Refresh the page
4. ✅ Try restarting Jitsi

### Meeting Not Listed
1. ✅ Verify session `scheduled_start` time has arrived
2. ✅ Check course is enrolled/published
3. ✅ Verify session is published (not draft)
4. ✅ Clear browser cache and refresh

---

## Important Notes

### URL Format Rules

- ✅ Room names can contain: letters, numbers, hyphens (`-`), underscores (`_`)
- ❌ Don't use: spaces, special characters, emojis
- ✅ Maximum length: 255 characters (but keep it short for usability)
- ✅ Case-insensitive: `AIVibeCoding112` = `aivibecoding112`

### Security

- **Public URL**: Anyone with the room name can join
  - `https://meet.jitsi/AIVibeCoding112` (no authentication)
- **Private via LMS**: Restricted through enrollment
  - Students access through LMS dashboard only
  - LMS tracks attendance automatically
  - Recommended: Use via LMS, not direct URL

### Best Practices

1. **Before Class (15 min early)**
   - Schedule the session
   - Test your audio/video
   - Verify Jitsi loads properly

2. **During Class**
   - Mute when not speaking
   - Encourage students to ask questions
   - Use hand raise feature
   - Record if needed (check local laws)

3. **After Class**
   - Wait for last student to leave
   - End recording if active
   - Update session status to "completed"
   - Note attendance

---

## API Endpoints (For Developers)

If you need to integrate Jitsi elsewhere:

```javascript
// Access Jitsi API in your code
// From: resources/views/courses/live-session.blade.php

var api = new JitsiMeetExternalAPI(domain, options);

// Listen for events
api.addEventListener('videoConferenceJoined', function() {
    console.log('User joined');
    // Update attendance, send notifications, etc.
});

api.addEventListener('videoConferenceLeft', function() {
    console.log('User left');
    // Calculate duration, mark as attended, etc.
});

// Execute commands
api.executeCommand('displayName', 'Your Name');
api.executeCommand('startRecording', 'dropbox'); // Start recording
api.executeCommand('stopRecording', 'dropbox');  // Stop recording
```

---

## Summary: Your Meeting Link

For room name: **AIVibeCoding112**

| Access Method | URL | Notes |
|---------------|-----|-------|
| **LMS Dashboard** | `/courses/3/live-session/show` | Recommended - tracks attendance |
| **Direct Jitsi** | `https://meet.jitsi/AIVibeCoding112` | Public - no attendance tracking |
| **Custom Domain** | `https://your-domain.com/AIVibeCoding112` | If self-hosted |

**To Start:** Just open the LMS session page at the scheduled start time! Jitsi loads automatically. 🎉

---

**Last Updated:** October 11, 2026  
**Jitsi Version:** Latest (meet.jitsi API)  
**LMS Feature:** Live Sessions Calendar System
