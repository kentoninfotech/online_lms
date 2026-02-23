# Email Verification Setup Guide

## Overview
The LearnSmart LMS now requires users to verify their email address before logging in. This enhances security and ensures valid communication channels.

## What Changed

### 1. **Registration Process**
- New users register with a role: Student (Learner), Instructor (Tutor), or Parent
- After registration, users receive a verification email
- They must click the verification link before they can log in

### 2. **User Model**
- User model now implements `MustVerifyEmail` interface
- Email verification is enforced at the middleware level

### 3. **Route Protection**
The following routes now require email verification:
- `admin.dashboard` - Admin Dashboard
- `instructor.dashboard` - Instructor Dashboard
- `student.dashboard` - Student Dashboard
- `parent.dashboard` - Parent Dashboard
- All role-specific protected routes with `verified` middleware

### 4. **Automatic Email Verification Routes**
The authentication routes now include:
- `GET /email/verify` - Shows verification pending screen
- `GET /email/verify/{id}/{hash}` - Verifies email with token
- `POST /email/resend` - Resends verification email

## SMTP Configuration

### Option 1: Gmail (Recommended for Testing)

1. Enable 2-Factor Authentication on your Google Account
2. Generate an [App Password](https://myaccount.google.com/apppasswords)
3. Update your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="LearnSmart Academy"
```

### Option 2: Mailgun

1. Create a [Mailgun account](https://www.mailgun.com)
2. Get your API key and domain
3. Update your `.env` file:

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-api-key
MAIL_FROM_ADDRESS=noreply@your-domain.mailgun.org
MAIL_FROM_NAME="LearnSmart Academy"
```

### Option 3: SendGrid

1. Create a [SendGrid account](https://sendgrid.com)
2. Generate an API key
3. Update your `.env` file:

```env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your-sendgrid-api-key
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="LearnSmart Academy"
```

### Option 4: Local Testing with Mailtrap

1. Create a [Mailtrap account](https://mailtrap.io)
2. Get SMTP credentials from your inbox
3. Update your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=test@example.com
MAIL_FROM_NAME="LearnSmart Academy"
```

### Option 5: Development Only (Mailtrap Log)

For local development without sending real emails:

```env
MAIL_MAILER=log
```

Check emails in `storage/logs/laravel.log`

## Testing Email Verification

### 1. **Test Registration**
```bash
# Access registration page
http://localhost:8000/register

# Fill the form with:
# - Full Name: Test User
# - Email: test@example.com
# - Role: Student
# - Password: TestPassword123
# - Accept Terms
```

### 2. **Check Email Logs**
If using LOG driver:
```bash
tail -f storage/logs/laravel.log
```

### 3. **Verify Email Manually** (if using log driver)
```bash
# Use Laravel Tinker to mark email as verified
php artisan tinker
> User::where('email', 'test@example.com')->update(['email_verified_at' => now()])
```

### 4. **Test SMTP Connection**
```bash
php artisan tinker
> Mail::raw('Test email', function($m) {
    $m->to('your-email@example.com')
      ->subject('Test from LearnSmart');
  })
```

## Email Verification View

The default Laravel verification view is used. Users will see:
1. **Verification Pending Page** after registration
2. **Resend Button** if they didn't receive the email
3. **Verification Link** in the email they receive

## Customizing Email Verification

### Send Custom Notification

Edit `RegisterController.php` to send a custom email:

```php
// After user creation
$user->notify(new App\Notifications\EmailVerificationNotification());
```

### Create Custom Verification Notification

```bash
php artisan make:notification EmailVerificationNotification
```

## Troubleshooting

### "SMTP authentication failed"
- Check MAIL_USERNAME and MAIL_PASSWORD
- Verify MAIL_HOST and MAIL_PORT are correct
- For Gmail, use App Password not your regular password

### "Connection timeout"
- Check MAIL_HOST is reachable: `ping smtp.gmail.com`
- Verify MAIL_PORT (usually 587 for TLS, 465 for SSL)
- Check firewall rules

### Emails not being sent
- Set `MAIL_MAILER=log` to see logs
- Check `storage/logs/laravel.log`
- Verify sender domain (SPAM folder)

### User stuck on verification page
- Use Tinker to mark as verified: 
```bash
php artisan tinker
> User::find(1)->markEmailAsVerified()
```

## Database Migrations

Email verification uses the existing `email_verified_at` column in the users table.
No new migrations needed.

## Security Notes

1. **Never commit SMTP credentials** to version control
2. **Use environment variables** for sensitive data
3. **Rotate API keys** regularly
4. **Monitor email logs** for suspicious activity
5. Test email deliverability with services like [Mail-tester](https://www.mail-tester.com)

## User Experience Flow

```
1. User registers
   ↓
2. Account created (email_verified_at = NULL)
   ↓
3. Verification email sent
   ↓
4. User clicks verify link
   ↓
5. email_verified_at timestamp set
   ↓
6. User can now login
```

## Admin Dashboard Email Settings

Future enhancement: Add email configuration UI in Admin > Settings > Email Configuration
to allow admins to manage SMTP settings from the dashboard.
