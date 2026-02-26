# COINMAC Hard-Coded References - Migration Summary

## Overview
All user-facing hard-coded "COINMAC Inc" and related company name references have been replaced with dynamic values from the admin-saved company name settings.

## Changes Made

### 1. **Auth Views** ✅
- [resources/views/auth/login.blade.php](resources/views/auth/login.blade.php)
  - Page title: Now uses `HomepageSetting::getSetting('branding', 'site_name')`
  - Company name header: Now displays dynamic site name

- [resources/views/auth/register.blade.php](resources/views/auth/register.blade.php)
  - Page title: Updated to use dynamic `site_name`
  - Welcome message: Uses dynamic site name

- [resources/views/auth/verify.blade.php](resources/views/auth/verify.blade.php)
  - Page title: Now uses dynamic `site_name` setting

### 2. **Layout Files** ✅
- [resources/views/layouts/auth.blade.php](resources/views/layouts/auth.blade.php)
  - Meta description: Uses `site_name` and `site_tagline` from branding settings
  - Meta author: Uses dynamic `site_name`

### 3. **Email Templates** ✅
- [resources/views/emails/contact-response.blade.php](resources/views/emails/contact-response.blade.php)
  - Support team signature: Uses dynamic `site_name`
  - Contact info: Uses dynamic `email_value` and `phone_value` from contact settings

- [app/Mail/ContactResponseMail.php](app/Mail/ContactResponseMail.php)
  - Email subject: Now dynamically includes the site name from branding settings

### 4. **Course Pages** ✅
- [resources/views/courses/index.blade.php](resources/views/courses/index.blade.php)
  - Testimonial: Updated to reference dynamic site name instead of "COINMAC's courses"
  - Contact section fallback: Now uses dynamic `email_value` and `phone_value`

- [resources/views/courses/all-courses.blade.php](resources/views/courses/all-courses.blade.php)
  - Page title: Uses dynamic `site_name` setting
  - CTA text: References dynamic site name

### 5. **Payment Pages** ✅
- [resources/views/courses/payments/bank-transfer.blade.php](resources/views/courses/payments/bank-transfer.blade.php)
  - Bank account name: Now prioritizes environment variable, then falls back to admin-saved `site_name`

## Settings Used

The implementation uses the following admin-configurable settings:

### Branding Section
- `site_name` - Company/site name (used as primary company name everywhere)
- `site_tagline` - Company tagline

### Contact Section
- `email_value` - Contact email address
- `phone_value` - Contact phone number

### Pages Section
- `landing_page_title` - Landing page title
- `all_courses_page_title` - All courses page title

## Admin Configuration

Admins can modify these settings from the admin panel:

1. **For Site Name**: 
   - Go to Admin → Site Builder → Logos
   - Update "Site Name" field

2. **For Contact Info**:
   - Go to Admin → Homepage Settings → Contact Section
   - Update Email, Phone, Address, Hours fields

3. **For Page Titles**:
   - Go to Admin → Site Builder → Page Titles
   - Update individual page titles

## Fallback Behavior

All locations have sensible fallback values in case settings aren't configured:
- Site name: Defaults to "LMS Inc" or "LMS"
- Email: Defaults to "info@example.org"
- Phone: Defaults to "+234 (0) 806 563 2882"
- Page titles: Include generic or branded fallback text

## Remaining Hard-Coded References

The following still contain "COINMAC" as they are:
1. **Database Seeders** - Initial seed data (admins can modify in database)
2. **Admin UI Defaults** - Preview defaults in admin editing forms
3. **HomepageSettingController** - Initial default configuration values

These are acceptable as they are developer-level configurations that users/admins can modify through the admin interface.

## Benefits

✅ **Fully Dynamic**: Company name can be changed from admin panel without code changes  
✅ **Consistent**: All references use the same source of truth  
✅ **Flexible**: Different environments can have different company names  
✅ **Maintainable**: No need to search/replace throughout codebase  
✅ **Professional**: Easy white-labeling capability  

## Testing Recommendations

1. Update site name in Admin → Site Builder → Logos
2. Verify it appears in:
   - Page titles (browser tab)
   - Login/Register pages
   - Email templates
   - Course pages
   - All other user-facing areas

3. Update contact information in Admin → Homepage Settings
4. Verify updated contact details appear on course page

---

**Status**: ✅ **COMPLETE** - All user-facing hard-coded references have been replaced with dynamic admin settings.
