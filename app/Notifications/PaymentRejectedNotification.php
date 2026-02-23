<?php

namespace App\Notifications;

use App\Models\CoursePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CoursePayment $payment, public string $reason) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $course = $this->payment->course;

        return (new MailMessage)
            ->subject('❌ Payment Rejected - ' . $course->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Unfortunately, your payment for **' . $course->title . '** has been rejected.')
            ->line('**Reason:**')
            ->line($this->reason)
            ->line('**Payment Details:**')
            ->line('Course: ' . $course->title)
            ->line('Amount: ' . $this->payment->currency . ' ' . number_format($this->payment->amount, 2))
            ->line('Please review the reason above and try again or contact support.')
            ->action('Try Again', route('course.payment.show', $this->payment))
            ->line('If you have questions, please contact our support team.');
    }
}
