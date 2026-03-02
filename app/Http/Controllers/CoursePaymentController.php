<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursePayment;
use App\Models\CourseEnrollee;
use App\Models\User;
use App\Notifications\PaymentPendingApprovalNotification;
use App\Notifications\PaymentReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class CoursePaymentController extends Controller
{
    /**
     * Show payment method selection page
     */
    public function showPaymentMethods(CoursePayment $payment)
    {
        // Verify user owns this payment
        if ($payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $payment->load('course', 'enrollment');

        return view('courses.payments.select-method', compact('payment'));
    }

    /**
     * Show Paystack payment page
     */
    public function payWithPaystack(CoursePayment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $payment->load('course', 'user');

        return view('courses.payments.paystack', compact('payment'));
    }

    /**
     * Show bank transfer payment page
     */
    public function payWithBank(CoursePayment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $payment->load('course', 'user');

        return view('courses.payments.bank-transfer', compact('payment'));
    }

    /**
     * Upload payment evidence
     */
    public function uploadEvidence(Request $request, CoursePayment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:paystack,bank',
            'payment_evidence_amount' => 'required|numeric|min:0.01',
            'payer_name' => 'required|string|max:255',
            'payment_evidence_path' => 'required|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Store the file
            if ($request->hasFile('payment_evidence_path')) {
                $file = $request->file('payment_evidence_path');
                $filename = Auth::id() . '-' . time() . '.' . $file->getClientOriginalExtension();
                $validated['payment_evidence_path'] = 'storage/' . $file->storeAs('uploads/courses', $filename, 'public');
            }

            // Update payment record
            $payment->update([
                'payment_method' => $validated['payment_method'],
                'payment_evidence_amount' => $validated['payment_evidence_amount'],
                'payer_name' => $validated['payer_name'],
                'payment_evidence_path' => $validated['payment_evidence_path'],
                'approval_status' => 'pending',
                'status' => 'pending', // Waiting for admin approval
            ]);

            DB::commit();

            // Notify all admins about the pending payment
            $admins = User::where('user_type', 'admin')->get();
            Notification::send($admins, new PaymentPendingApprovalNotification($payment));

            // Notify student that payment was received
            $payment->user->notify(new PaymentReceivedNotification($payment));

            return redirect()->route('course.payment.pending', $payment)
                ->with('success', 'Payment evidence uploaded successfully. Please wait for admin approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload payment evidence: ' . $e->getMessage());
        }
    }

    /**
     * Show pending payment status page
     */
    public function showPendingStatus(CoursePayment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $payment->load('course', 'enrollment.user');

        return view('courses.payments.pending', compact('payment'));
    }

    /**
     * Admin: List all payments for approval
     */
    public function adminIndex()
    {
        $this->authorize('isAdmin');

        $payments = CoursePayment::with('user', 'course', 'enrollment')
            ->where('approval_status', 'pending')
            ->orderByDesc('created_at')
            ->paginate(20);

        $approvedPayments = CoursePayment::with('user', 'course')
            ->where('approval_status', 'approved')
            ->orderByDesc('approved_at')
            ->limit(10)
            ->get();

        $rejectedPayments = CoursePayment::with('user', 'course')
            ->where('approval_status', 'rejected')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        return view('admin.course-payments.index', compact('payments', 'approvedPayments', 'rejectedPayments'));
    }

    /**
     * Admin: View payment details
     */
    public function adminShow(CoursePayment $payment)
    {
        $this->authorize('isAdmin');

        $payment->load('user', 'course', 'enrollment', 'approver');

        return view('admin.course-payments.show', compact('payment'));
    }

    /**
     * Admin: Approve payment
     */
    public function approve(Request $request, CoursePayment $payment)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'approval_notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Update payment status
            $payment->update([
                'approval_status' => 'approved',
                'status' => 'completed',
                'paid_at' => now(),
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $validated['approval_notes'] ?? null,
            ]);

            // Update enrollment status to active
            $enrollment = $payment->enrollment;
            $enrollment->update([
                'status' => 'active',
                'payment_status' => 'completed',
                'amount_paid' => $payment->amount,
                'payment_date' => now(),
            ]);

            DB::commit();

            // Send approval notification
            $payment->user->notify(new \App\Notifications\PaymentApprovedNotification($payment, $enrollment));

return redirect()->route('admin.course-payments.index')
                ->with('success', 'Payment approved successfully. User has been notified.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve payment: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Reject payment
     */
    public function reject(Request $request, CoursePayment $payment)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'approval_notes' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Update payment status
            $payment->update([
                'approval_status' => 'rejected',
                'approval_notes' => $validated['approval_notes'],
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Update enrollment status to cancelled
            $enrollment = $payment->enrollment;
            $enrollment->update([
                'status' => 'cancelled',
            ]);

            DB::commit();

            // Send rejection notification
            $payment->user->notify(new \App\Notifications\PaymentRejectedNotification($payment, $validated['approval_notes']));

            return redirect()->route('admin.course-payments.index')
                ->with('success', 'Payment rejected. User has been notified.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject payment: ' . $e->getMessage());
        }
    }
}
