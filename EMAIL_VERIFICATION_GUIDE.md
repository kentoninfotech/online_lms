# Email Verification Setup & Testing Guide

## Overview
The registration verification email system is fully configured with:
- ✅ Professional email template with logo
- ✅ 24-hour expiration links
- ✅ Dynamic branding (site name, logo)
- ✅ Admin testing interface
- ✅ Command-line testing tools
- ✅ Troubleshooting guide

## Email Template Features

### Visual Design
- **Custom Logo Support**: Pulls from branding settings or fallback images
- **Professional Layout**: Gradient header, organized content sections
- **Responsive Design**: Works on mobile, tablet, and desktop
- **Email Client Compatible**: Optimized for Gmail, Outlook, Apple Mail, etc.

### Content Sections
1. **Header** - Logo + Welcome message
2. **Greeting** - Personalized with user name
3. **Main Message** - Clear explanation of why email was sent
4. **Steps Section** - 3-step verification process
5. **CTA Button** - Prominent "Verify Email Address" button
6. **Alternative Link** - For clients that don't support buttons
7. **Expiration Notice** - Clear 24-hour expiry time
8. **Footer** - Company info + social links + legal links

## Quick Start: Test Registration Email

### Option 1: Using Admin Dashboard (Recommended)
1. **Navigate to**: `/admin/email-testing`
2. **Enter your email** and name
3. **Click "Send Test Email"**
4. **Check your inbox** (or spam folder)
5. **Verify all elements**:
   - Logo appears correctly
   - Text is formatted nicely
   - Button is clickable
   - Link works and verifies your account

### Option 2: Using Command Line
```bash
# Run this command in your project directory
php artisan email:test-verification your-email@example.com

# Interactive mode (it will ask for email)
php artisan email:test-verification
```

### Option 3: Test During User Registration
1. Go to `/register`
2. Fill out registration form with new email
3. Complete CAPTCHA (if enabled)
4. Accept terms and register
5. Check email for verification message

## Email Configuration

### Current Setup
Check the Admin Panel at `/admin/email-testing/config` to view:
- Mail driver (SMTP, Mailgun, etc.)
- From email address
- From name
- SMTP settings (if applicable)

### Setup Gmail SMTP
Perfect for development and small deployments:

1. **Enable 2FA** on your Google Account
2. **Generate App Password**:
   - Go to: https://myaccount.google.com/apppasswords
   - Select "Mail" → "Windows (or other device)"
   - Copy the 16-character password
3. **Update `.env` file**:
   ```
   MAIL_DRIVER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_ENCRYPTION=tls
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=xxxx xxxx xxxx xxxx
   MAIL_FROM_ADDRESS=your-email@gmail.com
   MAIL_FROM_NAME="Your Site Name"
   ```
4. **Restart your queue worker** (if using async):
   ```bash
   php artisan queue:work
   ```

### Setup Mailgun (Recommended for Production)
Professional email service with good deliverability:

1. **Create Account**: https://mailgun.com
2. **Verify Domain**: Add your domain and verify DNS records
3. **Get SMTP Credentials**: From Sending → Domain Settings
4. **Update `.env` file**:
   ```
   MAIL_DRIVER=smtp
   MAIL_HOST=smtp.mailgun.org
   MAIL_PORT=587
   MAIL_ENCRYPTION=tls
   MAIL_USERNAME=postmaster@yourdomain.com
   MAIL_PASSWORD=your-mailgun-password
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="Your Site Name"
   ```

## Email Logo Configuration

### Using Default Logo
1. Place logo at: `/public/assets/images/logo.png` or `.svg`
2. Email automatically picks it up

### Using Dynamic Logo from Branding Settings
1. Go to **Admin → Site Builder → Branding**
2. Upload logo images (light & dark versions)
3. Email pulls the configured logo

### Logo Requirements
- Format: PNG, SVG, JPG
- Size: < 100KB (optimized for email)
- Dimensions: Recommended 200x80px maximum
- Doesn't display? Check:
  - File exists and is readable
  - File size isn't too large
  - File path is correct

## Testing Verification Email Flow

### Complete User Journey Test
1. **Register New User**
   - Go to `/register`
   - Fill form with test email
   - Submit registration
   
2. **Receive Email**
   - Check inbox (usually arrives in 2-10 seconds)
   - Check spam folder if not found
   - Verify logo displays
   - Verify content is readable

3. **Click Verification Link**
   - Click "Verify Email Address" button or link
   - Should redirect to dashboard
   - Should see success message

4. **Test Account Access**
   - Try logging in: Should work without issues
   - Dashboard should load normally

### Email Element Checklist
- [ ] Email received in inbox
- [ ] Logo displays correctly
- [ ] Header has gradient background
- [ ] User name appears in greeting
- [ ] "Verify Email Address" button is clickable
- [ ] Alternative link is present
- [ ] Expiration notice shows 24 hours
- [ ] Footer contains company name
- [ ] Links are not broken
- [ ] Text formatting is preserved
- [ ] Works on mobile view
- [ ] Professional appearance

## Troubleshooting

### Email Not Received
**Problem**: No email arrives after registration or test

**Solutions**:
1. **Check spam/junk folder** first
2. **Verify email configuration**:
   - Go to `/admin/email-testing/config`
   - Confirm driver and credentials
3. **Test SMTP connection**:
   ```bash
   php artisan tinker
   > Mail::raw('test', function($m) { $m->to('test@example.com'); });
   ```
4. **Check queue** (if using async):
   ```bash
   php artisan queue:work
   ```
5. **Review logs**:
   - Check: `/storage/logs/laravel.log`
   - Look for mail errors

### Logo Not Displaying in Email
**Problem**: Logo shows as broken image in email

**Solutions**:
1. **Verify file exists**:
   ```bash
   ls -la public/assets/images/logo.png
   ```
2. **Check file permissions**:
   ```bash
   chmod 644 public/assets/images/logo.png
   ```
3. **Try different image**:
   - Try `.svg` instead of `.png`
   - Ensure image is < 100KB
4. **Upload via admin panel**:
   - Go to: Admin → Site Builder → Branding
   - Upload fresh logo file

### Email Template Broken
**Problem**: Email layout is messed up or text overlapping

**Solutions**:
1. **Test in different email clients**:
   - Gmail, Outlook, Yahoo, Apple Mail
   - Different clients render CSS differently
2. **Check email file**:
   ```
   /resources/views/emails/verify-email.blade.php
   ```
3. **Use email testing service**:
   - Try: https://www.litmus.com (preview in 70+ clients)
   - Or: https://www.emailonacid.com

### Queue Issues
**Problem**: Emails stuck in queue or not sending

**Solutions**:
1. **Start queue worker**:
   ```bash
   php artisan queue:work
   ```
2. **Clear failed jobs**:
   ```bash
   php artisan queue:clear
   php artisan queue:failed
   ```
3. **Retry failed**:
   ```bash
   php artisan queue:retry all
   ```
4. **Use sync queue** (for testing):
   ```
   QUEUE_CONNECTION=sync
   ```
   Then emails send immediately without queue.

## Email Files & Locations

### Key Files
- **Notification Class**: `/app/Notifications/VerifyEmailNotification.php`
- **Email Template**: `/resources/views/emails/verify-email.blade.php`
- **Testing Controller**: `/app/Http/Controllers/Admin/EmailTestController.php`
- **Test Command**: `/app/Console/Commands/TestVerificationEmail.php`

### Configuration Files
- **Mail Config**: `/config/mail.php`
- **Queue Config**: `/config/queue.php`
- **Environment**: `.env` file (MAIL_* variables)

## Testing Commands

```bash
# Test verification email from CLI
php artisan email:test-verification user@example.com

# Start queue worker (required if QUEUE_CONNECTION != sync)
php artisan queue:work

# Monitor queue
php artisan queue:work --verbose

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear queue
php artisan queue:clear

# Test mail connection
php artisan tinker
> Mail::raw('Test email content', function($m) { $m->to('test@example.com'); });
> exit
```

## Production Deployment Checklist

- [ ] Email driver configured (SMTP/Mailgun)
- [ ] MAIL_FROM_ADDRESS set to valid domain
- [ ] Logo uploaded and visible
- [ ] Queue worker running (via supervisor/systemd)
- [ ] Test email sent and received
- [ ] Email verified successfully
- [ ] User can login after verification
- [ ] Monitor logs for email errors
- [ ] SPF/DKIM records configured (for Mailgun/custom domain)
- [ ] Email rate limits configured if needed

## Support & Help

### Admin Testing Interface
- **URL**: `/admin/email-testing`
- **Shows**: Mail driver, configuration, testing form
- **Features**: Send test emails, view config, troubleshooting tips

### Command Line Help
```bash
php artisan help email:test-verification
```

### Database Verification
Check user's email verification status:
```bash
php artisan tinker
> User::find(1)->email_verified_at
> User::where('email', 'test@example.com')->first()
```

## Next Steps

1. ✅ **Test email** using admin panel or CLI
2. ✅ **Verify logo** appears correctly
3. ✅ **Create test user** and complete registration
4. ✅ **Click verification link** to verify it works
5. ✅ **Confirm user** can login after verification
6. ✅ **Monitor logs** for any errors
7. ✅ **Deploy to production** with confidence

---

**Last Updated**: March 3, 2026
**Version**: 1.0
**Status**: Production Ready ✅
