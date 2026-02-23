# Email Verification System - Setup & Testing Guide

## 📧 Current Configuration

**File:** `.env`

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=mail.jobiz.ng
MAIL_PORT=465
MAIL_USERNAME=info@jobiz.ng
MAIL_PASSWORD=@@BIZjob22
MAIL_FROM_ADDRESS="info@jobiz.ng"
MAIL_FROM_NAME="Coinmac International Inc"
MAIL_ENCRYPTION=ssl
```

**Status:** ✅ Configured for **Gmail-compatible SMTP** with SSL encryption

---

## 🔄 How Email Verification Works

### Registration Flow
1. **User registers** → Account created with `email_verified_at = NULL`
2. **Verification email sent** → Automatic email with unique verification link
3. **User tries to login** → Redirected to verification page if email not verified
4. **User clicks link** → Email verified, `email_verified_at` is set
5. **User logs in again** → Full access to dashboard granted

### Key Files
- **Controller:** [`LoginController.php`](app/Http/Controllers/Auth/LoginController.php) - Checks `hasVerifiedEmail()` on login
- **View:** [`verify.blade.php`](resources/views/auth/verify.blade.php) - Verification page with resend button
- **Routes:** `verification.notice` (show page), `verification.verify` (confirm), `verification.resend` (send new link)

---

## ✅ Testing Verification Email Delivery

### Method 1: Manual Test via Web UI (RECOMMENDED)

**Step 1: Create Test Account**
```
URL: http://localhost:8000/register

Fill form:
- Name: Test User
- Email: test@example.com  (use your real email to receive the link)
- Password: test123456
- Confirm: test123456
- User Type: Student

Then submit
```

**Step 2: Check Email**
- Go to your email inbox (test@example.com)
- Look for email from `info@jobiz.ng`
- Click the verification link
- You'll see "Email verified successfully!"
- Return to `http://localhost:8000/login` and log in

**Step 3: Verify Success**
- You should be logged in and redirected to your dashboard
- ✅ Email verification is working!

---

### Method 2: Test via Artisan Commands

**Create test user:**
```bash
php artisan tinker

# In Tinker (paste these one by one):
$user = User::create(['name' => 'Test', 'email' => 'test@example.com', 'password' => bcrypt('test123456'), 'user_type' => 'student']);
$user->update(['email_verified_at' => null]);
```

**Send verification email:**
```bash
# Still in Tinker:
use Illuminate\Auth\Notifications\VerifyEmail;
$user->notify(new VerifyEmail());
echo "Email sent!";
exit
```

**Check if it was sent:**
- Check inbox at test@example.com
- If using `log` mailer: Check `storage/logs/laravel.log`

---

## 🔍 Troubleshooting

### Email Not Received?

#### 1. **Check .env Configuration**
```bash
# These MUST be set correctly:
✓ MAIL_MAILER=smtp (NOT 'log')
✓ MAIL_HOST=mail.jobiz.ng
✓ MAIL_PORT=465 (for SSL) or 587 (for TLS)
✓ MAIL_USERNAME=info@jobiz.ng
✓ MAIL_PASSWORD=@@BIZjob22
✓ MAIL_ENCRYPTION=ssl (for port 465)
✓ MAIL_FROM_ADDRESS="info@jobiz.ng"
```

After any changes:
```bash
php artisan config:cache
php artisan config:clear  # if issues persist
```

#### 2. **Test SMTP Credentials**
Test your email credentials in your email client or using Thunderbird:
- Server: `mail.jobiz.ng`
- Port: `465`
- Security: `SSL`
- Username: `info@jobiz.ng`
- Password: `@@BIZjob22`

If it fails here, the problem is with SMTP credentials, not the app.

#### 3. **Check Firewall/Network**
```bash
# Test SMTP port connectivity:
# On Windows PowerShell:
Test-NetConnection -ComputerName mail.jobiz.ng -Port 465

# Should see: TcpTestSucceeded : True
```

#### 4. **View Log Files**
```bash
# Check for mailing errors:
tail -f storage/logs/laravel.log

# Look for lines with "Mail" or "Exception"
```

#### 5. **Enable SMTP Debug**
Add to `.env`:
```dotenv
MAIL_DEBUG=true
```

Then check logs for detailed SMTP conversation.

---

## 📋 Email Verification Features Implemented

### ✅ Features
- [x] Email verification required on registration
- [x] Beautiful verification page with clear instructions
- [x] "Resend Verification Email" button
- [x] Live preview of email address needing verification
- [x] Automatic logout if unverified user tries to access dashboard
- [x] Step-by-step guidance (inbox, spam folder, link click)
- [x] Dark-themed auth page matching login design
- [x] Session message: "Please verify your email address"

### 📧 Email Template
Used: Laravel's built-in `VerifyEmail` notification
- Professional HTML email
- Verification button with unique link
- Expiration: 60 minutes (configurable)
- Timezone-aware timestamps

---

## 🔐 Security Measures

1. **Signed URLs** - Verification links are cryptographically signed
2. **Expiration** - Links expire after 1 hour by default
3. **One-time Use** - Once verified, email cannot be re-verified
4. **Queue Support** - Emails can be queued for async sending

---

## 📞 Support

If emails aren't sending after following all steps:

1. **Contact your email provider** (jobiz.ng SMTP)
2. **Check if SMTP account credentials are correct**
3. **Verify IP whitelist isn't blocking the server**
4. **Consider using alternative SMTP:**
   - Gmail SMTP (requires app password)
   - SendGrid
   - Mailgun
   - AWS SES

---

## 🚀 Next Steps (Optional Enhancements)

To implement in future:
- [ ] Queue email sending with `php artisan queue:work`
- [ ] Custom email template (instead of Laravel's default)
- [ ] Resend limit (max 3 times per hour)
- [ ] Notification on admin when user verifies
- [ ] Auto-delete unverified accounts after 7 days

---

**Last Updated:** February 21, 2026
**Status:** ✅ Email verification system active and ready for testing
