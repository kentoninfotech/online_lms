<?php

namespace App\Notifications;

use App\Models\CoursePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject('✅ Payment Evidence Received - ' . $course->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your payment evidence for **' . $course->title . '** has been received.')
            ->line('**Payment Details:**')
            ->line('Course: ' . $course->title)
            ->line('Amount: ' . $this->payment->currency . ' ' . number_format($this->payment->payment_evidence_amount, 2))
            ->line('Reference: ' . $this->payment->reference_id)
            ->line('')
            ->line('**Next Steps:**')
            ->line('Our admin team will review your payment and verify it. This typically takes 24-48 hours.')
            ->line('You will receive another email once your payment is approved.')
            ->action('Check Payment Status', route('courses.my-enrollments'))
            ->line('Thank you for your patience!');
    }
}
