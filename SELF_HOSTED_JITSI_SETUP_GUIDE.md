# Self-Hosted Jitsi Setup & Configuration Guide

## Overview

This guide covers setting up a **complete self-hosted Jitsi server** using Docker, replacing the cloud version (`meet.jitsi`) with your own private instance.

---

## ⚙️ System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                   YOUR LMS APPLICATION                      │
├─────────────────────────────────────────────────────────────┤
│ Students/Instructors access:                                │
│ Dashboard → Live Sessions → Jitsi loads                     │
└─────────────┬───────────────────────────────────────────────┘
              │
              ↓ (HTTPS Request)
        ┌─────────────────────────────────────┐
        │   SELF-HOSTED JITSI SERVER          │
        │   (running in Docker containers)    │
        ├─────────────────────────────────────┤
        │ Services:                           │
        │ ├─ Jitsi Web UI (Port 80/443)      │
        │ ├─ Prosody XMPP (Port 5222/5269)   │
        │ ├─ Jicofo (Conference Focus)       │
        │ ├─ JVB (Videobridge Port 10000)    │
        │ └─ Etherpad (Port 9001)            │
        └─────────────────────────────────────┘
```

---

## 🚀 Prerequisites

### System Requirements
- **OS**: Linux (Ubuntu 20.04 LTS or newer) or Windows with WSL2/Docker Desktop
- **Memory**: Minimum 4GB RAM (8GB recommended)
- **CPU**: 2 cores minimum (4 cores recommended)
- **Storage**: 20GB free space
- **Network**: Open ports 80, 443, 10000 (UDP/TCP)
- **Domain**: Optional but recommended (e.g., `meet.jitsi.local` for local dev)

### Software
- Docker (19.03+)
- Docker Compose (1.25+)
- Git

### Installation
```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install docker.io docker-compose git

# Enable Docker without sudo (optional)
sudo usermod -aG docker $USER
newgrp docker

# Verify installation
docker --version
docker-compose --version
```

---

## 📋 Step 1: Configure Environment

### 1.1 Check `.env` Configuration

Your project `.env` should have:

```env
# ✅ CORRECT CONFIGURATION FOR SELF-HOSTED
JITSI_DOMAIN=meet.jitsi.local
JITSI_SERVER_URL=https://meet.jitsi.local
JITSI_SELF_HOSTED=true
JITSI_APP_ID=             # (optional, leave empty)
JITSI_APP_SECRET=         # (optional, leave empty)
```

**For Production** (with real domain):
```env
JITSI_DOMAIN=meet.yourdomain.com
JITSI_SERVER_URL=https://meet.yourdomain.com
JITSI_SELF_HOSTED=true
```

### 1.2 Verify Configuration File

Config is stored in: `config/services.php`

```php
'jitsi' => [
    'domain' => env('JITSI_DOMAIN', 'meet.jitsi.local'),
    'app_id' => env('JITSI_APP_ID'),
    'app_secret' => env('JITSI_APP_SECRET'),
    'self_hosted' => env('JITSI_SELF_HOSTED', false),
    'server_url' => env('JITSI_SERVER_URL', 'https://meet.jitsi.local'),
],
```

---

## 🐳 Step 2: Start Self-Hosted Jitsi with Docker

### 2.1 Verify Docker Compose File

File location: `docker-compose.jitsi.yml`

The file includes:
- **jitsi-web**: Web UI (ports 80, 443)
- **jitsi-prosody**: XMPP server
- **jitsi-jicofo**: Conference focus
- **jitsi-jvb**: Videobridge (port 10000)
- **jitsi-etherpad**: Collaborative notes

### 2.2 Start Jitsi Services

```bash
# Navigate to project directory
cd /path/to/online_lms

# Start Jitsi in detached mode
docker-compose -f docker-compose.jitsi.yml up -d

# View logs
docker-compose -f docker-compose.jitsi.yml logs -f

# Check running containers
docker ps

# Stop services (when done)
docker-compose -f docker-compose.jitsi.yml down
```

### 2.3 Expected Output

```
Creating jitsi-prosody  ... done
Creating jitsi-web      ... done
Creating jitsi-jicofo   ... done
Creating jitsi-jvb      ... done
Creating jitsi-etherpad ... done
```

---

## 🌐 Step 3: Local Domain Configuration

### 3.1 For Local Development (Windows/macOS)

Edit your **hosts file**:

**Windows:** `C:\Windows\System32\drivers\etc\hosts`
```
127.0.0.1       meet.jitsi.local
127.0.0.1       jitsi-web
```

**Linux/macOS:** `/etc/hosts`
```bash
sudo nano /etc/hosts
# Add:
127.0.0.1       meet.jitsi.local
127.0.0.1       jitsi-web
```

### 3.2 For Production with Real Domain

1. **Get SSL Certificate** (Let's Encrypt):
```bash
# Using Certbot
sudo apt-get install certbot
sudo certbot certonly --standalone -d meet.yourdomain.com
```

2. **Mount certificate in Docker** (update docker-compose.jitsi.yml):
```yaml
jitsi-web:
  volumes:
    - /etc/letsencrypt/live/meet.yourdomain.com/fullchain.pem:/etc/jitsi/web/certs/cert.pem:ro
    - /etc/letsencrypt/live/meet.yourdomain.com/privkey.pem:/etc/jitsi/web/certs/key.pem:ro
```

3. **Configure DNS** to point to your server:
```
meet.yourdomain.com  →  your.server.ip.address
```

---

## 🧪 Step 4: Verify Self-Hosted Jitsi is Running

### 4.1 Test Health Endpoints

```bash
# Test if Jitsi web UI is accessible
curl -I http://meet.jitsi.local

# Expected: HTTP/1.1 200 OK or 403 (if not configured properly)

# Test Jitsi API
curl http://meet.jitsi.local/api/v1/config

# Test if it's actually your self-hosted instance (NOT meet.jitsi)
# Should show your custom domain, not meet.jitsi
```

### 4.2 Check Docker Container Health

```bash
# View container status
docker-compose -f docker-compose.jitsi.yml ps

# Should show:
# NAME              STATUS        PORTS
# jitsi-web         Up (healthy)  0.0.0.0:80->80/tcp, 0.0.0.0:443->443/tcp
# jitsi-prosody     Up            
# jitsi-jicofo      Up            
# jitsi-jvb         Up            0.0.0.0:10000->10000/udp
# jitsi-etherpad    Up            0.0.0.0:9001->9001/tcp

# View logs for any errors
docker-compose -f docker-compose.jitsi.yml logs jitsi-web
docker-compose -f docker-compose.jitsi.yml logs jitsi-jvb
```

### 4.3 Manual Test in Browser

1. Open browser: `http://meet.jitsi.local`
2. Should see Jitsi Meet interface
3. Enter room name: `testroom123`
4. Click "Go"
5. Grant camera/microphone permissions
6. Should connect to **your self-hosted server** (not cloud)

---

## 📱 Step 5: Create Test Live Session

### 5.1 In Your LMS

1. Login to Admin Dashboard
2. Go to: **Admin → Courses → Physics 101 → Live Sessions → Create**
3. Fill in form:
   ```
   Title: "Self-Hosted Jitsi Test"
   Facilitator: Your Name
   Start Time: Now or future time
   End Time: 1 hour later
   Session Type: Jitsi Meet
   Jitsi Room Name: test-self-hosted-001
   Chat Enabled: ✓ (checked)
   ```
4. Click "Schedule Session"

### 5.2 Verify Configuration is Used

The system should now:
- Use `meet.jitsi.local` domain (from `.env`)
- Load Jitsi from your self-hosted instance
- NOT use `meet.jitsi` cloud version
- Create room: `test-self-hosted-001`

---

## 🧑‍🏫 Step 6: Test Meeting - Invite People

### 6.1 As Facilitator (You)

```
1. Navigate to live session in LMS
2. Jitsi should load automatically
3. Browser asks: "Allow camera/microphone?"
4. Click [Allow]
5. You're in! (on your self-hosted server)

Features available:
✅ Video on/off
✅ Microphone on/off
✅ Screen sharing
✅ Chat
✅ Recording
```

### 6.2 Invite Students

```
Option 1: Via LMS (Recommended)
  ├─ Students get dashboard notification
  ├─ Click live session
  ├─ Jitsi loads automatically
  └─ Join meeting

Option 2: Share Direct Link
  ├─ Copy: http://meet.jitsi.local/test-self-hosted-001
  ├─ Share via email/chat
  ├─ Anyone can click to join
  └─ (No login required)

Option 3: QR Code
  ├─ Generate QR for: http://meet.jitsi.local/test-self-hosted-001
  ├─ Display on screen
  ├─ Students scan with phone
  └─ Joins meeting
```

### 6.3 During Meeting

**Facilitator Controls:**
```
🎤 Microphone     → Mute/unmute
📷 Camera         → Show/hide video
🖥️  Screen Share   → Share slides/content
💬 Chat           → Send messages
🔴 Record         → Save session
📊 Stats          → View connection quality
```

**Student Options:**
```
🎤 Microphone     → Mute/unmute
📷 Camera         → Show/hide video
🖥️  Screen Share   → Share content
💬 Chat           → Send messages
✋ Raise Hand      → Signal to ask question
```

---

## ✅ Verification Checklist

```
[✓] Docker and Docker Compose installed
[✓] .env configured with meet.jitsi.local
[✓] docker-compose.jitsi.yml file present
[✓] All 5 containers running (docker ps)
[✓] Hosts file updated with 127.0.0.1 meet.jitsi.local
[✓] http://meet.jitsi.local loads Jitsi UI
[✓] Can create test room and join
[✓] Can share screen and use chat
[✓] Microphone/camera works
[✓] Recording button available
[✓] LMS creates meeting with custom domain
[✓] Students can access from dashboard
[✓] Attendance auto-tracked
[✓] Meeting data stored locally (not on meet.jitsi servers)
```

---

## 🔧 Troubleshooting

### Jitsi Web Won't Load

**Problem:** Visiting `http://meet.jitsi.local` shows "Cannot connect"

**Solution:**
```bash
# 1. Verify container is running
docker ps | grep jitsi-web

# 2. Check container logs
docker-compose -f docker-compose.jitsi.yml logs jitsi-web

# 3. Restart containers
docker-compose -f docker-compose.jitsi.yml restart

# 4. Check if ports are available
sudo netstat -tulpn | grep -E '80|443|10000'
```

### Can't Join Video Conference

**Problem:** Jitsi UI loads but video conference won't start

**Solution:**
```bash
# 1. Check JVB container
docker-compose -f docker-compose.jitsi.yml logs jitsi-jvb

# 2. Verify port 10000 is open
docker ps | grep jvb
# Should show: 10000:10000/udp, 10000:10000/tcp

# 3. Check firewall
sudo ufw allow 10000/udp
sudo ufw allow 10000/tcp

# 4. Check network connectivity
ping jitsi-jvb
```

### Students Can't Connect

**Problem:** Students see Jitsi UI but can't join room

**Solution:**
```bash
# 1. Verify XMPP/Prosody is running
docker-compose -f docker-compose.jitsi.yml logs jitsi-prosody

# 2. Check Jicofo
docker-compose -f docker-compose.jitsi.yml logs jitsi-jicofo

# 3. Verify domain is correct in .env
grep JITSI .env

# 4. Clear browser cache and refresh
# In browser: Ctrl+Shift+Delete → Clear all

# 5. Restart entire stack
docker-compose -f docker-compose.jitsi.yml down
docker-compose -f docker-compose.jitsi.yml up -d
```

### Recording Not Working

**Problem:** Record button doesn't work or errors

**Solution:**
```bash
# 1. Ensure recording volume is mounted
# In docker-compose.jitsi.yml check:
# jitsi-web:
#   volumes:
#     - ./jitsi/recordings:/recordings

# 2. Create recordings directory
mkdir -p ./jitsi/recordings
chmod 777 ./jitsi/recordings

# 3. Restart containers
docker-compose -f docker-compose.jitsi.yml restart

# 4. Check permissions
ls -la ./jitsi/recordings
```

---

## 🔐 Security Best Practices

### 1. **Change Default Passwords**

Edit `docker-compose.jitsi.yml` and change:
```yaml
environment:
  JICOFO_COMPONENT_SECRET: "change-this-to-random-string"
  JVB_SECRET: "change-this-to-random-string"
  JIGASI_XMPP_PASSWORD: "change-this-to-random-string"
  JIBRI_XMPP_PASSWORD: "change-this-to-random-string"
```

Generate random passwords:
```bash
openssl rand -base64 32
# Repeat for each secret
```

### 2. **Enable SSL/TLS**

```bash
# Generate self-signed certificate (dev only)
mkdir -p ./jitsi/web/certs
openssl req -x509 -newkey rsa:4096 -keyout ./jitsi/web/certs/key.pem \
  -out ./jitsi/web/certs/cert.pem -days 365 -nodes
```

### 3. **Restrict Access**

```bash
# Only allow from your network
sudo ufw allow from 192.168.0.0/24 to any port 80
sudo ufw allow from 192.168.0.0/24 to any port 443
```

### 4. **Regular Updates**

```bash
# Update Docker images monthly
docker-compose -f docker-compose.jitsi.yml pull
docker-compose -f docker-compose.jitsi.yml up -d
```

---

## 📊 Monitoring & Logs

### View Real-Time Logs

```bash
# All services
docker-compose -f docker-compose.jitsi.yml logs -f

# Specific service
docker-compose -f docker-compose.jitsi.yml logs -f jitsi-jvb

# Last 100 lines
docker-compose -f docker-compose.jitsi.yml logs --tail=100

# With timestamps
docker-compose -f docker-compose.jitsi.yml logs -f -t
```

### Performance Stats

```bash
# View resource usage
docker stats

# Memory per container
docker ps --format 'table {{.Names}}\t{{.MemUsage}}'

# Disk usage
du -sh ./jitsi/*
```

---

## 🚀 Production Deployment

### On Real Server

1. **Use domain name** (not `meet.jitsi.local`)
   ```env
   JITSI_DOMAIN=meet.yourdomain.com
   JITSI_SERVER_URL=https://meet.yourdomain.com
   ```

2. **Configure SSL** with Let's Encrypt
3. **Open required ports**:
   - 80 (HTTP redirect)
   - 443 (HTTPS)
   - 10000 (Videobridge UDP/TCP)

4. **Set up DNS**:
   ```
   meet.yourdomain.com → your.server.ip
   ```

5. **Use environment file** for secrets:
   ```bash
   cp .env.example .env.production
   # Edit with production values
   docker-compose -f docker-compose.jitsi.yml --env-file .env.production up -d
   ```

---

## 📞 Support & Resources

- **Jitsi Official**: https://jitsi.org/
- **Docker Jitsi**: https://github.com/jitsi/docker-jitsi-meet
- **Jitsi Community Forum**: https://community.jitsi.org/
- **Troubleshooting**: See sections above

---

## 📝 Summary

**What You've Accomplished:**

✅ Set up self-hosted Jitsi with Docker  
✅ Configured LMS to use your instance  
✅ Created test meeting  
✅ Invited and tested with users  
✅ Verified video/audio/chat/recording  
✅ All data stays on your server (not meet.jitsi)  

**Result:**

Your LMS now has a **completely private, self-hosted video conferencing system** that doesn't depend on the public `meet.jitsi` cloud service!

---

*Last Updated: October 11, 2026*  
*Self-Hosted Jitsi: Production Ready ✅*
