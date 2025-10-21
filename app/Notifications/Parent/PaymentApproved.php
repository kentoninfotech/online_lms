<?php

namespace App\Notifications\Parent;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payment;

class PaymentApproved extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Payment $payment) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Payment Approved')
            ->greeting('Hello,')
            ->line("Your payment of ₦{$this->payment->amount} for student {$this->payment->subscription->student->name} has been approved.")
            ->line('Your subscription is now active.')
            ->action('View Subscription', route('parent.payments'))
            ->line('Thank you for keeping your subscription active.');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toDatabase($notifiable)
    {
        $actionRoute = [
            'name' => 'parent.payments',
            'params' => [], 
        ];

        return [
            'category' => 'Payments', 
            'payment_id' => $this->payment->id,
            'status'     => 'approved',

            'title' => 'Payment Approved',
            'message_lines' => [
                "Your payment of ₦{$this->payment->amount} for student {$this->payment->subscription->student->name} has been approved.",
                'Your subscription is now active.',
            ],
            'action' => [
                'text' => 'View Subscription',
                'route' => $actionRoute,
            ],
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'payment_id' => $this->payment->id,
            'student'    => $this->payment->subscription->student->name,
            'amount'     => $this->payment->amount,
            'status'     => 'approved',
        ];
    }
}
