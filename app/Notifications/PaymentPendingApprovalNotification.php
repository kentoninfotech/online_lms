<?php

namespace App\Notifications;

use App\Models\CoursePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentPendingApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CoursePayment $payment) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $course = $this->payment->course;
        $student = $this->payment->user;

        return (new MailMessage)
            ->subject('⏳ Payment Evidence Received - Waiting for Approval: ' . $course->title)
            ->greeting('Hello Admin,')
            ->line('A new payment evidence has been submitted and is waiting for your approval.')
            ->line('**Student Details:**')
            ->line('Name: ' . $student->name)
            ->line('Email: ' . $student->email)
            ->line('')
            ->line('**Payment Details:**')
            ->line('Course: ' . $course->title)
            ->line('Reference ID: ' . $this->payment->reference_id)
            ->line('Amount Transferred: ' . $this->payment->currency . ' ' . number_format($this->payment->payment_evidence_amount, 2))
            ->line('Payment Method: Bank Transfer')
            ->line('Payer Name: ' . $this->payment->payer_name)
            ->line('Submitted: ' . $this->payment->created_at->format('M d, Y H:i A'))
            ->action('Review Payment Evidence', route('admin.course-payments.show', $this->payment))
            ->line('Please review and approve or reject this payment.');
    }
}
