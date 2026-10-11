# Self-Hosted Jitsi Testing Guide

## 🧪 Complete Testing Procedure

This guide walks through testing your self-hosted Jitsi installation end-to-end.

---

## ✅ Phase 1: Infrastructure Testing (5 minutes)

### 1.1 Verify Docker Containers

```bash
# Check if all containers are running
docker-compose -f docker-compose.jitsi.yml ps

# Expected output:
# NAME              STATUS          PORTS
# jitsi-web         Up (healthy)    0.0.0.0:80->80/tcp, :::80->80/tcp
# jitsi-prosody     Up              
# jitsi-jicofo      Up              
# jitsi-jvb         Up              0.0.0.0:10000->10000/udp
# jitsi-etherpad    Up              0.0.0.0:9001->9001/tcp
```

**Status Check:**
- [ ] All containers showing "Up" status
- [ ] JVB showing port 10000 mapped
- [ ] Web UI showing port 80/443 mapped

### 1.2 Test Network Connectivity

```bash
# Test Jitsi Web UI
curl -I http://meet.jitsi.local

# Expected: HTTP/1.1 200 OK

# Test XMPP server
curl -I http://meet.jitsi.local:5222

# Test Etherpad
curl -I http://meet.jitsi.local:9001
```

**Connectivity Check:**
- [ ] Web UI responds (HTTP 200)
- [ ] No timeout errors
- [ ] No "Connection refused" errors

### 1.3 View Startup Logs

```bash
# Check for errors during startup
docker-compose -f docker-compose.jitsi.yml logs | grep -i "error"

# Expected: No critical errors
```

**Logs Check:**
- [ ] No "Cannot connect" errors
- [ ] No "Port already in use" errors
- [ ] No "Certificate" errors

---

## ✅ Phase 2: Web UI Testing (5 minutes)

### 2.1 Access Jitsi Web Interface

**Steps:**
1. Open browser: `http://meet.jitsi.local`
2. Should see: Jitsi Meet homepage
3. Input box for room name

**Verification:**
- [ ] Page loads without errors
- [ ] Jitsi logo visible
- [ ] "Start a new meeting" text visible
- [ ] Input field for room name present

### 2.2 Create Test Room

**Steps:**
1. In input field, type: `test-infrastructure-001`
2. Click "Go" or press Enter
3. Wait for page to load (10-15 seconds)
4. Should ask for camera/microphone permission

**Verification:**
- [ ] Room name accepted
- [ ] Page loaded (not showing errors)
- [ ] Browser permission dialog appears
- [ ] Connected to your Jitsi (not meet.jitsi)

### 2.3 Grant Permissions

**Steps:**
1. Click [Allow] for camera
2. Click [Allow] for microphone
3. Wait for video to load (5-10 seconds)

**Verification:**
- [ ] Permissions granted
- [ ] Video feed shows
- [ ] Your camera preview visible
- [ ] Microphone indicator shows

### 2.4 Test Basic Features

```
Actions to test:
┌─ [🎤] Microphone button
│  └─ Toggle on/off, verify mutes
│
├─ [📷] Camera button
│  └─ Toggle on/off, verify shows/hides video
│
├─ [⚙️] Settings
│  └─ Open settings menu, should load
│
├─ [📊] Stats
│  └─ Shows connection stats
│
└─ [?] Help
   └─ Opens help menu
```

**Verification:**
- [ ] Microphone mutes audio
- [ ] Camera toggles video
- [ ] Settings menu opens
- [ ] Stats show bandwidth/connection
- [ ] Help menu displays

---

## ✅ Phase 3: LMS Integration Testing (10 minutes)

### 3.1 Configure .env File

**Verify .env has:**
```env
JITSI_DOMAIN=meet.jitsi.local
JITSI_SERVER_URL=https://meet.jitsi.local
JITSI_SELF_HOSTED=true
```

**Verification:**
- [ ] .env file updated
- [ ] Domain is NOT `meet.jitsi` (that's cloud)
- [ ] Domain is `meet.jitsi.local` (your server)

### 3.2 Create Test Live Session

**Steps:**
1. Login to LMS Admin Dashboard
2. Navigate: **Admin → Courses → Course ID → Live Sessions → Create**
3. Fill form:
   ```
   Title: "Self-Hosted Test - Oct 11"
   Facilitator: Your Name
   Start Time: Now
   End Time: 1 hour from now
   Session Type: Jitsi Meet
   Jitsi Room Name: lms-test-001
   Chat Enabled: ✓
   ```
4. Click "Schedule Session"

**Verification:**
- [ ] Session created successfully
- [ ] No errors on save
- [ ] Session appears in list
- [ ] Status shows "scheduled"

### 3.3 Test Session Access

**Steps:**
1. Navigate to: **Courses → [Your Course] → Live Sessions**
2. Should see session with [LIVE NOW] or [Scheduled] badge
3. Click on session
4. Page should load with Jitsi embedded

**Verification:**
- [ ] Session visible in list
- [ ] Correct title displayed
- [ ] Time/date showing correctly
- [ ] Jitsi player loading

### 3.4 Check Domain Configuration

**In browser console (F12):**
```javascript
// Check what domain is being used
console.log(document.querySelector('script[src*="external_api"]').src);

// Should show:
// https://meet.jitsi.local/external_api.js
// NOT https://meet.jitsi/external_api.js
```

**Verification:**
- [ ] Script loading from `meet.jitsi.local` (YOUR server)
- [ ] NOT loading from `meet.jitsi` (cloud)
- [ ] Protocol is https:// or http://

---

## ✅ Phase 4: Video Conference Testing (15 minutes)

### 4.1 Facilitator Joins Meeting

**Steps:**
1. In LMS, click the live session
2. Jitsi loads with empty room
3. Browser asks: "Allow camera/microphone?"
4. Click [Allow] for both
5. Wait for video to load (10 seconds)

**Verification:**
- [ ] Jitsi loads without errors
- [ ] Permission request appears
- [ ] Video shows your camera
- [ ] Audio indicator shows microphone
- [ ] No "Failed to connect" error

### 4.2 Test Solo Features

```
Test these features (solo):

🎤 Microphone
  ├─ [✓] Click to mute
  ├─ [✓] Click to unmute
  └─ [✓] Audio level shows

📷 Camera
  ├─ [✓] Click to disable
  ├─ [✓] Click to enable
  └─ [✓] Video shows/hides

💬 Chat
  ├─ [✓] Click to open chat panel
  ├─ [✓] Type message "Test"
  ├─ [✓] Press Enter to send
  └─ [✓] Message appears

🖥️ Share Screen
  ├─ [✓] Click share screen
  ├─ [✓] Select entire screen
  ├─ [✓] Screen displays to you
  └─ [✓] Stop sharing works

🔴 Record
  ├─ [✓] Click record button
  ├─ [✓] Recording starts (indicator shows)
  └─ [✓] Click to stop recording
```

**Verification (count passes):**
- [ ] Microphone: __ / 3 passed
- [ ] Camera: __ / 3 passed
- [ ] Chat: __ / 4 passed
- [ ] Screen Share: __ / 4 passed
- [ ] Recording: __ / 3 passed

### 4.3 Invite Second User (Manual Test)

**Steps:**
1. Copy meeting URL from browser address bar
   - Should be: `http://meet.jitsi.local/lms-test-001`
2. Open **new browser tab or incognito window**
3. Paste URL
4. For testing, use name: "Test Student"
5. Click "Go"
6. Allow camera/microphone
7. Should see facilitator's video

**Verification:**
- [ ] Two users can be in same room
- [ ] Can see each other's video
- [ ] Can hear each other (unmute both)
- [ ] Chat messages visible to both
- [ ] Participant count shows "2"

### 4.4 Test Interaction

**With two users in room:**

```
Interaction Tests:
├─ [✓] Both send chat messages
│  └─ Messages appear for both
│
├─ [✓] Facilitator sends, student hears
│  └─ Unmute both, speak, listen
│
├─ [✓] Student mutes, facilitator sees
│  └─ Mute indicator shows
│
├─ [✓] Student raises hand
│  └─ Hand appears for facilitator
│
└─ [✓] Share screen with both
   └─ Both see shared screen
```

**Verification (count passes):**
- [ ] Chat: __/2 interactions passed
- [ ] Audio: __/2 interactions passed
- [ ] Mute Indicator: __/1 passed
- [ ] Hand Raise: __/1 passed
- [ ] Screen Share: __/1 passed

---

## ✅ Phase 5: Multi-User Testing (10 minutes)

### 5.1 Add Third Participant

**Steps:**
1. Open another browser window/incognito
2. Same meeting URL
3. Name: "Test Student 2"
4. Join room

**Verification:**
- [ ] Three users in room
- [ ] Participant count shows "3"
- [ ] All three videos visible
- [ ] All can communicate

### 5.2 Test with Muted/Disabled Devices

**User 1 (Facilitator):** Audio ON, Video ON
**User 2:** Audio OFF (muted), Video ON
**User 3:** Audio ON, Video OFF

**Verification:**
- [ ] Status shows correctly for each user
- [ ] Facilitator can speak, others hear
- [ ] User 2 can unmute and speak
- [ ] User 3 can enable video
- [ ] Indicators update in real-time

---

## ✅ Phase 6: Performance Testing (5 minutes)

### 6.1 Monitor System Resources

**In terminal:**
```bash
# Watch container resources
docker stats

# Should see:
# NAME           CPU %  MEM USAGE / LIMIT
# jitsi-jvb      5-15%  400-600MB
# jitsi-prosody  2-5%   100-200MB
# jitsi-web      1-3%   50-100MB
```

**Verification:**
- [ ] CPU usage under 50%
- [ ] Memory usage under 2GB
- [ ] No "Out of memory" errors
- [ ] No container restarts

### 6.2 Test Video Quality

**Indicators to check:**
- [ ] Video smooth (not choppy)
- [ ] Audio clear (not lagging)
- [ ] No disconnections
- [ ] Stats show good bandwidth

**From stats panel:**
- [ ] Upload bandwidth: 1-3 Mbps
- [ ] Download bandwidth: 1-3 Mbps
- [ ] Packet loss: <2%
- [ ] RTT (ping): <100ms

---

## ✅ Phase 7: Data Verification

### 7.1 Check Meeting Data Storage

```bash
# Verify data is stored locally
ls -la ./jitsi/

# Should show directories:
# prosody/
# web/
# recordings/

# Check if recordings exist
ls -la ./jitsi/recordings/

# Should show recorded files (if recording was enabled)
```

**Verification:**
- [ ] `./jitsi/` directory exists
- [ ] Subdirectories present
- [ ] Recordings stored locally (not uploaded to meet.jitsi)
- [ ] All data private on your server

### 7.2 Verify Self-Hosted (Not Cloud)

**Proof test:**

```bash
# Compare with cloud version
echo "=== Your Self-Hosted Server ==="
curl -s http://meet.jitsi.local/api/v1/config | grep -o '"domain":"[^"]*"'

echo ""
echo "=== Cloud meet.jitsi (for comparison) ==="
curl -s https://meet.jitsi/api/v1/config | grep -o '"domain":"[^"]*"' 2>/dev/null || echo "(Different configuration)"
```

**Verification:**
- [ ] Your server shows `meet.jitsi.local`
- [ ] NOT `meet.jitsi`
- [ ] Proves you're using self-hosted, not cloud

---

## 📊 Test Results Summary

### Scoring Guide

```
Phase 1: Infrastructure      __/3 checks
Phase 2: Web UI              __/4 checks
Phase 3: LMS Integration     __/4 checks
Phase 4: Video Conference   __/18 checks
Phase 5: Multi-User         __/4 checks
Phase 6: Performance        __/4 checks
Phase 7: Data Verification  __/2 checks

TOTAL SCORE: __/39 checks passed
```

**Passing Criteria:**
- **PASSED**: 35+ checks (90%)
- **GOOD**: 33-34 checks (85%)
- **ACCEPTABLE**: 30-32 checks (77%)
- **NEEDS WORK**: <30 checks (<77%)

---

## 🎯 Quick Test Summary

### Fastest Route (5 minute test)

```
1. docker-compose -f docker-compose.jitsi.yml ps
   └─ Verify all containers running
   
2. curl -I http://meet.jitsi.local
   └─ Verify web UI responsive
   
3. Open http://meet.jitsi.local/test-room-123
   └─ Allow camera/microphone
   └─ Verify video loads
   
4. Login to LMS
   └─ Create live session with Jitsi
   └─ Click to join
   └─ Verify Jitsi loads in LMS
   
✅ RESULT: Self-hosted Jitsi working!
```

---

## 🆘 If Tests Fail

### Container Won't Start
```bash
# View error logs
docker-compose -f docker-compose.jitsi.yml logs jitsi-web

# Rebuild containers
docker-compose -f docker-compose.jitsi.yml down
docker-compose -f docker-compose.jitsi.yml up -d --build
```

### Can't Access http://meet.jitsi.local
```bash
# 1. Check hosts file
cat /etc/hosts  # Linux/Mac
type %SystemRoot%\System32\drivers\etc\hosts  # Windows

# Should contain: 127.0.0.1  meet.jitsi.local

# 2. Flush DNS cache
sudo systemctl restart systemd-resolved  # Linux
sudo dscacheutil -flushcache  # Mac
ipconfig /flushdns  # Windows

# 3. Try with IP
curl -I http://127.0.0.1
```

### No Video/Audio
```bash
# Check browser permissions
# In browser: Settings → Privacy → Camera/Microphone → Allow

# Check container logs
docker-compose -f docker-compose.jitsi.yml logs jitsi-jvb

# Test port 10000
netstat -an | grep 10000
```

---

## ✅ Final Checklist

```
INFRASTRUCTURE
[✓] All 5 Docker containers running
[✓] Ports 80, 443, 10000 accessible
[✓] No startup errors in logs

WEB UI
[✓] http://meet.jitsi.local loads
[✓] Can enter room name
[✓] Jitsi homepage displays

LMS INTEGRATION
[✓] .env configured for self-hosted
[✓] Live session created in LMS
[✓] Session loads Jitsi in dashboard

VIDEO CONFERENCE
[✓] Single user can join
[✓] Video/audio/chat works
[✓] Multiple users can join
[✓] All users can interact

DATA
[✓] No data sent to meet.jitsi
[✓] All data stored locally
[✓] Recordings saved on server

PERFORMANCE
[✓] System resources under 50% CPU
[✓] No memory errors
[✓] Video smooth, no lags
[✓] Bandwidth adequate

PRODUCTION READY
[✓] Self-hosted Jitsi confirmed
[✓] All tests passing
[✓] Ready for classroom use
```

---

**If all boxes checked: ✅ PRODUCTION READY!**

Your self-hosted Jitsi server is fully tested and ready for live teaching sessions!

---

*Last Updated: October 11, 2026*  
*Test Version: 1.0*  
*Status: Ready for Production ✅*
