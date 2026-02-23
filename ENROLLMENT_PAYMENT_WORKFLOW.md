# Course Enrollment & Payment Workflow - Complete Implementation

## Overview
The course enrollment and payment workflow is now **fully functional** with email notifications at each stage. Users can enroll in courses, select a payment method, and receive status updates throughout the approval process.

## Complete User Journey

### 1. Course Enrollment Form
**Route:** `GET /course/{course}/enroll`  
**View:** `resources/views/courses/enrollment.blade.php`  
**Controller:** `CourseEnrollmentController@create()`

Users click "Enroll Now" to see:
- Course title and details
- Available course dates (dropdown)
- Venue selection for each date
- Course fee prominently displayed
- Cancel and "Complete Enrollment" buttons

### 2. Submit Enrollment
**Route:** `POST /course/{course}/enroll`  
**Controller:** `CourseEnrollmentController@store()`

**What Happens:**
1. Validates: `course_date_id` and `course_venue_id`
2. Creates `CourseEnrollee` record with:
   - `status = 'pending'`
   - `payment_status = 'pending'`
   - `enrolled_at = now()`
3. Creates `CoursePayment` record with:
   - `status = 'pending'`
   - `approval_status = 'pending'`
   - `reference_id = 'CRS-{timestamp}-{userId}'`
4. **Redirects to:** Payment method selection page

### 3. Select Payment Method
**Route:** `GET /course-payment/{payment}`  
**View:** `resources/views/courses/payments/select-method.blade.php`  
**Controller:** `CoursePaymentController@showPaymentMethods()`

Users see:
- Course recap with amount due
- "Paystack" card - for card/online payments
- "Bank Transfer" card - for direct bank transfers
- Info banner explaining both methods
- Ownership verification (only payment owner can proceed)

## Payment Method Paths

### Path A: Paystack Payment

**1. Payment Page**
- **Route:** `GET /course-payment/{payment}/paystack`
- **View:** `resources/views/courses/payments/paystack.blade.php`
- **Controller:** `CoursePaymentController@payWithPaystack()`
- Displays Paystack payment widget
- User enters card details and completes payment

**2. After Payment**
- Paystack callback verified (requires webhook implementation)
- Admin approves payment
- User receives `PaymentApprovedNotification`

### Path B: Bank Transfer Payment

**1. Bank Transfer Form**
- **Route:** `GET /course-payment/{payment}/bank`
- **View:** `resources/views/courses/payments/bank-transfer.blade.php`
- **Controller:** `CoursePaymentController@payWithBank()`

Users see:
- Bank account details from environment variables:
  - `BANK_NAME` - e.g., "First Bank of Nigeria"
  - `BANK_ACCOUNT_NAME` - e.g., "Coinmac International Inc"
  - `BANK_ACCOUNT_NUMBER` - e.g., "3017934851"
  - `BANK_SORT_CODE` - e.g., "011"
- Instructions to include reference ID in transfer description
- Form fields:
  - Amount Paid (pre-filled with course fee)
  - Payer's Name (pre-filled with current user name)
  - Payment Evidence file (PDF, JPG, PNG - Max 5MB)

**2. Upload Payment Evidence**
- **Route:** `POST /course-payment/{payment}/upload-evidence`
- **Controller:** `CoursePaymentController@uploadEvidence()`

**What Happens:**
1. Validates form fields:
   - `payment_evidence_amount` (required, numeric)
   - `payer_name` (required, string)
   - `payment_evidence_path` (required file)
2. Stores file to: `storage/payment-evidence/{user_id}-{timestamp}.ext`
3. Updates `CoursePayment` record:
   - Sets `approval_status = 'pending'`
   - Sets `status = 'pending'`
4. **Notifications Sent:**
   - 📧 **To Admin(s):** `PaymentPendingApprovalNotification`
     - Includes: Student name, course, reference ID, amount
     - Contains: "Review Payment Evidence" action button
     - Links to: `admin.course-payments.show`
   - 📧 **To Student:** `PaymentReceivedNotification`
     - Confirms evidence received
     - States: 24-48 hour review time
     - Links to: My Enrollments page
5. **Redirects to:** Pending status page

**3. Pending Approval Page**
- **Route:** `GET /course-payment/{payment}/pending`
- **View:** `resources/views/courses/payments/pending.blade.php`
- **Controller:** `CoursePaymentController@showPendingStatus()`

Users see:
- Success message: "Payment evidence submitted successfully"
- Hourglass icon indicating pending status
- Course and payment recap
- Timeline visualization:
  - ✓ Payment Evidence Submitted
  - ⏳ Under Review
  - → Approval Decision
- Call-to-action buttons:
  - "Back to Course"
  - "My Enrollments"
- Note: "Typical Review Time: 24 hours"

## Admin Workflow

### Admin Payment Review
**Route:** `GET /admin/course-payments`  
**View:** `admin.course-payments.index`  
**Controller:** `CoursePaymentController@adminIndex()`

Admin sees:
- List of pending payments awaiting approval
- Recently approved/rejected payments
- Links to individual payment details

### Admin Approval/Rejection
**Routes:**
- `POST /admin/course-payments/{payment}/approve`
- `POST /admin/course-payments/{payment}/reject`

**When Admin Approves:**
1. Updates `CoursePayment`:
   - `approval_status = 'approved'`
   - `approved_by = admin_user_id`
   - `approved_at = now()`
2. Updates `CourseEnrollee`:
   - `status = 'active'`
   - `payment_status = 'completed'`
3. Sends `PaymentApprovedNotification` to student
4. Student can now access course

**When Admin Rejects:**
1. Updates `CoursePayment`:
   - `approval_status = 'rejected'`
   - Stores rejection notes
2. Sends `PaymentRejectedNotification` to student
3. Student notified of rejection reason

## Email Notifications

### 1. Payment Pending Approval (Admin) ✅ NEW
**Notification:** `App\Notifications\PaymentPendingApprovalNotification`
**Recipient:** All users with `user_type = 'admin'`
**Triggered:** When student uploads bank transfer evidence

**Email Contains:**
- Student name and email
- Course title
- Payment reference ID
- Amount transferred
- Payer name
- Date submitted
- "Review Payment Evidence" action button
- Subject: "⏳ Payment Evidence Received - Waiting for Approval"

### 2. Payment Received (Student) ✅ NEW
**Notification:** `App\Notifications\PaymentReceivedNotification`
**Recipient:** The student who uploaded evidence
**Triggered:** Immediately after evidence upload

**Email Contains:**
- Course title
- Amount received
- Reference ID
- Timeline: "24-48 hours"
- "Check Payment Status" button
- Subject: "✅ Payment Evidence Received"

### 3. Payment Approved (Student) ✅ EXISTING
**Notification:** `App\Notifications\PaymentApprovedNotification`
**Recipient:** The student whose payment was approved
**Triggered:** When admin approves payment

**Email Contains:**
- Approval confirmation
- Course access confirmation
- "View Course" action button
- Subject: "✅ Payment Approved"

### 4. Payment Rejected (Student) ✅ EXISTING
**Notification:** `App\Notifications\PaymentRejectedNotification`
**Recipient:** The student whose payment was rejected
**Triggered:** When admin rejects payment

**Email Contains:**
- Rejection notice
- Rejection reason/notes from admin
- Next steps information
- Subject: "❌ Payment Rejected"

## Database Tables & Fields

### CoursePayment Table
```
- id (primary key)
- course_enrollee_id (FK)
- user_id (FK)
- course_id (FK)
- amount (decimal)
- currency (enum, default: 'NGN')
- reference_id (string, unique)
- status (enum: pending, completed, failed)
- payment_method (enum: paystack, bank)
- approval_status (enum: pending, approved, rejected) ⭐ KEY FIELD
- payment_evidence_path (string, nullable)
- payment_evidence_amount (decimal, nullable)
- payer_name (string, nullable)
- approved_by (FK to User, nullable)
- approved_at (timestamp, nullable)
- created_at, updated_at
```

### CourseEnrollee Table
```
- id (primary key)
- user_id (FK)
- course_id (FK)
- course_date_id (FK)
- course_venue_id (FK)
- status (enum: pending, active, completed, cancelled)
- payment_status (enum: pending, completed)
- enrolled_at (timestamp)
- created_at, updated_at
```

## Routes Summary

```
GET  /course/{course}/enroll
POST /course/{course}/enroll
GET  /course-payment/{payment}
GET  /course-payment/{payment}/bank
GET  /course-payment/{payment}/paystack
POST /course-payment/{payment}/upload-evidence
GET  /course-payment/{payment}/pending
GET  /admin/course-payments
GET  /admin/course-payments/{payment}
POST /admin/course-payments/{payment}/approve
POST /admin/course-payments/{payment}/reject
```

## Implementation Checklist

- ✅ Enrollment form (create/store endpoints)
- ✅ Payment method selection UI
- ✅ Paystack payment integration started
- ✅ Bank transfer form with account details
- ✅ Payment evidence upload handler
- ✅ Pending approval status page
- ✅ Admin payment review interface
- ✅ Admin approval workflow
- ✅ Admin rejection workflow
- ✅ Email notification to admin (PAYMENT_PENDING) - **NEW**
- ✅ Email notification to student (PAYMENT_RECEIVED) - **NEW**
- ✅ Email notification on approval
- ✅ Email notification on rejection
- ✅ File storage for payment evidence
- ✅ Transaction atomicity (DB::beginTransaction)
- ✅ User authorization checks
- ✅ Error handling and validation

## Testing Checklist

To test the complete workflow:

1. **Enroll a Student:**
   - Go to a course page
   - Click "Enroll Now"
   - Select a date and venue
   - Click "Complete Enrollment"
   - Should redirect to payment method selection

2. **Choose Bank Transfer:**
   - Click "Bank Transfer" card
   - Verify bank details display
   - Upload a sample PDF/image as evidence
   - Click "Submit Payment Evidence"
   - Should redirect to pending status page

3. **Verify Notifications (Check Queue):**
   ```bash
   # In new terminal, watch queue jobs
   php artisan queue:listen
   ```
   - Should see 2 notifications queued (Admin + Student)
   - Emails sent to admin and student

4. **Admin Approval:**
   - Go to `/admin/course-payments`
   - Find pending payment
   - Click "Review Payment Evidence"
   - Click "Approve" button
   - Student should receive approval email
   - Course enrollment should activate

## Key Files Modified

- `app/Http/Controllers/CoursePaymentController.php` - Added notification dispatching
- `app/Notifications/PaymentPendingApprovalNotification.php` - **NEW**
- `app/Notifications/PaymentReceivedNotification.php` - **NEW**

## Key Files (Pre-existing)

- `app/Http/Controllers/CourseEnrollmentController.php`
- `resources/views/courses/enrollment.blade.php`
- `resources/views/courses/payments/select-method.blade.php`
- `resources/views/courses/payments/bank-transfer.blade.php`
- `resources/views/courses/payments/paystack.blade.php`
- `resources/views/courses/payments/pending.blade.php`
- `app/Models/CoursePayment.php`
- `app/Models/CourseEnrollee.php`
- `app/Models/Course.php`

## Environment Variables Required

```env
# For bank transfer details display
BANK_NAME=First Bank of Nigeria
BANK_ACCOUNT_NAME=Coinmac International Inc
BANK_ACCOUNT_NUMBER=3017934851
BANK_SORT_CODE=011

# For email notifications
MAIL_FROM_ADDRESS=admin@coinmac.com
MAIL_FROM_NAME=Coinmac Learning
MAIL_DRIVER=smtp
MAIL_HOST=smtp.jobiz.ng
MAIL_PORT=587
MAIL_USERNAME=your_email@jobiz.ng
MAIL_PASSWORD=your_password

# For queued notifications to work
QUEUE_CONNECTION=database  # or redis
```

## What's New

The system now has **complete end-to-end functionality**:

1. ✅ **Students can enroll** → form submission creates enrollment record
2. ✅ **Students select payment method** → visual method selection page
3. ✅ **Students upload bank evidence** → file stored securely
4. ✅ **Admin gets notified** → `PaymentPendingApprovalNotification` email
5. ✅ **Student gets confirmation** → `PaymentReceivedNotification` email
6. ✅ **Admin can approve/reject** → existing workflow maintained
7. ✅ **Student gets approval/rejection email** → existing notifications work
8. ✅ **Course enrollment activates** → upon payment approval

## Next Steps / Future Enhancements

1. **Paystack Webhook Integration** - Handle Paystack callback to auto-approve payments
2. **Payment History View** - Let students view payment history
3. **Payment Receipt Generation** - PDF receipt generation on approval
4. **Bulk Payment Actions** - Admin bulk approve/reject multiple payments
5. **Payment Reminders** - Auto-email students if payment pending too long
6. **SMS Notifications** - Add SMS for critical status updates (using Nexmo/Twilio)
7. **Payment Stats Dashboard** - Better admin dashboard with payment analytics
