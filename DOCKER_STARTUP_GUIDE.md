# Self-Hosted Jitsi - Docker Startup Instructions

## 🚀 Quick Start (Windows)

### Step 1: Start Docker Desktop

#### Option A: From Start Menu
```
1. Press [Win] key
2. Type "Docker Desktop"
3. Click "Docker Desktop" to launch
4. Wait 2-3 minutes for Docker to initialize
```

#### Option B: From Command Line
```powershell
# Open PowerShell as Administrator and run:
Start-Process "C:\Program Files\Docker\Docker\Docker.exe"

# Wait for Docker to start (2-3 minutes)
# You'll see Docker icon in system tray when ready
```

#### Option C: Check Status
```powershell
# After Docker has started, verify it's running:
docker ps

# Should output something like:
# CONTAINER ID   IMAGE     COMMAND   CREATED   STATUS    PORTS     NAMES
# (empty if no containers running)
```

---

### Step 2: Configure Hosts File (Windows)

This step allows your browser to resolve `meet.jitsi.local` to `localhost`.

#### As Administrator (Recommended)

1. **Open Notepad as Administrator**
   - Right-click Notepad → "Run as administrator"

2. **Open the hosts file**
   - File → Open
   - Navigate to: `C:\Windows\System32\drivers\etc\`
   - Change file type to "All Files (*.*)"
   - Select `hosts` file (no extension)
   - Click Open

3. **Add this line at the end**
   ```
   127.0.0.1  meet.jitsi.local
   ```

4. **Save**
   - Ctrl + S
   - Close Notepad

5. **Flush DNS cache** (PowerShell as Admin)
   ```powershell
   ipconfig /flushdns
   ```

---

### Step 3: Navigate to Project Directory

```powershell
cd C:\Users\Ogochukwu\Desktop\PROJECTS\PHP\online_lms
```

---

### Step 4: Start Jitsi Containers

```powershell
# Start all containers in the background
docker-compose -f docker-compose.jitsi.yml up -d

# Expected output:
# Creating network "online_lms_jitsi-network" with driver "bridge"
# Pulling jitsi/web:latest
# Pulling jitsi/prosody:latest
# ... (downloading images)
# Creating jitsi-prosody ... done
# Creating jitsi-web ... done
# Creating jitsi-jicofo ... done
# Creating jitsi-jvb ... done
# Creating jitsi-etherpad ... done
```

---

### Step 5: Wait for Containers to Initialize

```powershell
# Check status (wait ~30 seconds for full startup)
docker-compose -f docker-compose.jitsi.yml ps

# Should show:
# NAME           STATUS
# jitsi-web      Up (healthy)
# jitsi-prosody  Up
# jitsi-jicofo   Up
# jitsi-jvb      Up
# jitsi-etherpad Up
```

---

### Step 6: Test Jitsi Web UI

```powershell
# Test with curl
curl -I http://127.0.0.1

# Or open in browser:
# http://meet.jitsi.local
# (if hosts file configured, otherwise use http://127.0.0.1)
```

---

## 📋 Full Testing Checklist

Once Docker is running, follow this checklist:

### Phase 1: Infrastructure Verification
- [ ] Step 1: Docker Desktop running
- [ ] Step 2: Hosts file configured (127.0.0.1 meet.jitsi.local)
- [ ] Step 3: Navigated to project folder
- [ ] Step 4: Ran docker-compose up -d
- [ ] Step 5: All containers showing "Up"

### Phase 2: Web UI Test
```powershell
# In browser, go to:
http://localhost:80
# OR
http://meet.jitsi.local

# You should see:
# - Jitsi Meet homepage
# - Text input for room name
# - "Go" button
```

- [ ] Jitsi homepage loads
- [ ] No errors in browser console (F12)
- [ ] Can enter room name
- [ ] "Go" button functional

### Phase 3: Test Meeting Room
```powershell
# In browser, go to:
http://localhost:80/test-room-001
# (or http://meet.jitsi.local/test-room-001)

# Steps:
1. Allow camera permission
2. Allow microphone permission
3. Wait for video to load
4. Should see your video feed
```

- [ ] Room loads without errors
- [ ] Browser permission dialog appears
- [ ] Video preview shows
- [ ] Audio indicator works
- [ ] Can toggle mute/camera

### Phase 4: Multi-User Test
```powershell
# 1. Keep first browser window open in the meeting
# 2. Open NEW browser window/tab/incognito
# 3. Go to: http://localhost:80/test-room-001
# 4. Give it a different name (e.g., "Test Student")
# 5. Allow permissions
```

- [ ] Second user joins same room
- [ ] Can see both videos
- [ ] Participant count shows "2"
- [ ] Chat works between users
- [ ] Can unmute and hear each other

### Phase 5: LMS Integration Test
```
1. Open LMS Admin Dashboard
2. Go to: Admin → Courses → [Your Course] → Live Sessions
3. Click: "Create Live Session"
4. Fill form:
   - Title: "Self-Hosted Test"
   - Session Type: Jitsi Meet
   - Jitsi Room: test-lms-integration
   - Start Time: Now
   - End Time: +1 hour
5. Click: "Schedule Session"
6. Go to course dashboard
7. Click the live session
8. Verify Jitsi loads in the LMS (not from meet.jitsi)
```

- [ ] Live session created
- [ ] Session appears in LMS
- [ ] Jitsi loads within LMS iframe
- [ ] Verify domain is localhost (not meet.jitsi)

---

## 🆘 Troubleshooting

### Docker Won't Start
```powershell
# Check if Docker is installed
docker --version

# If not installed, download from:
# https://www.docker.com/products/docker-desktop

# If installed but not running:
# Right-click Docker Desktop → Settings → General
# Check "Start Docker Desktop when you log in"
```

### "Cannot connect to Docker daemon"
```powershell
# Docker Desktop not running
# Solution: Start Docker Desktop from Start Menu
# Wait 2-3 minutes for it to initialize
```

### Port 80 Already in Use
```powershell
# Check what's using port 80
netstat -ano | findstr :80

# If Apache/IIS is running:
# Stop Apache: net stop Apache2.4
# Stop IIS: net stop http.sys

# Or change Jitsi port in docker-compose.jitsi.yml:
# Change: "80:80" to "8080:80"
# Then access at: http://localhost:8080
```

### Can't Access meet.jitsi.local
```powershell
# 1. Verify hosts file entry
type C:\Windows\System32\drivers\etc\hosts

# Should contain: 127.0.0.1  meet.jitsi.local

# 2. Flush DNS
ipconfig /flushdns

# 3. Try with IP instead
http://127.0.0.1

# 4. Check if curl works
curl -I http://127.0.0.1
```

### No Camera/Microphone Access
```
1. In browser: Settings → Privacy → Microphone/Camera
2. Ensure permissions are granted for localhost
3. Check Windows Settings:
   - Settings → Privacy & Security → Camera
   - Settings → Privacy & Security → Microphone
   - Enable for "Camera" app or your browser
```

### Docker Containers Keep Stopping
```powershell
# Check container logs
docker-compose -f docker-compose.jitsi.yml logs

# Specific service logs
docker-compose -f docker-compose.jitsi.yml logs jitsi-web

# Rebuild containers
docker-compose -f docker-compose.jitsi.yml down
docker-compose -f docker-compose.jitsi.yml up -d --build
```

---

## ✅ Verification Commands

Run these commands to verify everything is working:

```powershell
# 1. Check Docker is running
docker info

# 2. Check Jitsi containers
docker-compose -f docker-compose.jitsi.yml ps

# 3. Check port 80 is accessible
curl -I http://127.0.0.1

# 4. Check health of web container
docker inspect jitsi-web --format='{{json .State.Health}}'

# 5. View recent logs
docker-compose -f docker-compose.jitsi.yml logs --tail=50

# 6. Test specific container
docker exec jitsi-web curl -s http://localhost/

# 7. Check disk space (containers need ~2GB)
$vol = Get-Volume -DriveLetter C
Write-Host "Free Space: $([math]::Round($vol.SizeRemaining / 1GB, 2)) GB"
```

---

## 📊 Testing Progress

```
SETUP PHASE
[ ] Step 1: Docker Desktop started
[ ] Step 2: Hosts file updated
[ ] Step 3: Project directory selected
[ ] Step 4: docker-compose up -d executed
[ ] Step 5: All containers running

WEB UI PHASE
[ ] Jitsi homepage loads
[ ] Room creation works
[ ] No console errors

MEETING PHASE
[ ] Single user meeting works
[ ] Camera/mic functional
[ ] Multi-user meeting works
[ ] Chat functional
[ ] All features work

LMS INTEGRATION
[ ] Live session created
[ ] Jitsi loads in LMS
[ ] Domain verified (localhost, not meet.jitsi)

PRODUCTION READY
[ ] All tests passing
[ ] Self-hosted confirmed
[ ] Ready for classroom use
```

---

## 🎯 Quick Testing Script

Save this as `test-jitsi.ps1` and run:

```powershell
# test-jitsi.ps1

Write-Host "=== Jitsi Self-Hosted Testing ===" -ForegroundColor Green

# Check Docker
Write-Host "`n1. Checking Docker..."
try {
    docker ps | Out-Null
    Write-Host "✓ Docker running" -ForegroundColor Green
} catch {
    Write-Host "✗ Docker not running" -ForegroundColor Red
    exit 1
}

# Check containers
Write-Host "`n2. Checking Jitsi containers..."
$containers = docker-compose -f docker-compose.jitsi.yml ps --services 2>/dev/null
if ($containers) {
    Write-Host "✓ Containers found: $containers" -ForegroundColor Green
} else {
    Write-Host "✗ No containers running" -ForegroundColor Red
}

# Test port 80
Write-Host "`n3. Testing port 80..."
try {
    $response = curl -I http://127.0.0.1 -TimeoutSec 5 -ErrorAction Stop
    if ($response) {
        Write-Host "✓ Port 80 responding" -ForegroundColor Green
    }
} catch {
    Write-Host "✗ Port 80 not responding" -ForegroundColor Red
}

# Check specific services
Write-Host "`n4. Service Status:"
docker-compose -f docker-compose.jitsi.yml ps --format "table {{.Service}}\t{{.Status}}"

Write-Host "`n=== Testing Complete ===" -ForegroundColor Green
```

---

## Next Steps

1. **Start Docker Desktop**
2. **Add hosts file entry**
3. **Run `docker-compose -f docker-compose.jitsi.yml up -d`**
4. **Wait 30 seconds for containers to start**
5. **Test at http://localhost or http://meet.jitsi.local**
6. **Follow Phase 1-5 testing steps**
7. **Report results**

---

## ⚠️ Important Notes

- **First startup:** Docker images download (~500MB), may take 5-10 minutes
- **Hosts file:** Required for `meet.jitsi.local` to work (use IP `127.0.0.1` as alternative)
- **Ports:** Port 80 must be available (change in docker-compose if needed)
- **Memory:** Requires ~2GB free RAM
- **Browser:** Use Firefox or Chrome, tested on latest versions

---

*Ready to test self-hosted Jitsi? Start with Step 1!*

