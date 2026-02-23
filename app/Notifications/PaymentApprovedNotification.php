<?php

namespace App\Notifications;

use App\Models\CoursePayment;
use App\Models\CourseEnrollee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CoursePayment $payment, public CourseEnrollee $enrollment) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $course = $this->payment->course;

        return (new MailMessage)
            ->subject('✅ Payment Approved - ' . $course->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your payment for **' . $course->title . '** has been approved!')
            ->line('**Payment Details:**')
            ->line('Course: ' . $course->title)
            ->line('Amount: ' . $this->payment->currency . ' ' . number_format($this->payment->amount, 2))
            ->line('Date: ' . $this->payment->approved_at->format('M d, Y H:i A'))
            ->line('You can now access the course content.')
            ->action('View Course', route('courses.learn', $course))
            ->line('Thank you for enrolling!');
    }
}
