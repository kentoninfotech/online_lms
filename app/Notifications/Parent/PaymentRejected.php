<?php

namespace App\Notifications\Parent;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payment;

class PaymentRejected extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Payment $payment, public string $reason) {}

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
            ->subject('Payment Rejected')
            ->greeting('Hello,')
            ->line("Your payment of ₦{$this->payment->amount} for student {$this->payment->subscription->student->name} has been rejected.")
            ->line("Reason: {$this->reason}")
            ->line('Please re-submit valid payment evidence or contact support.')
            ->action('Re-submit Payment', url('parent.payments.create')) //DEFINE ROUTE LATER
            ->line('Thank you.');
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
            'status'     => 'rejected',
            'reason'     => $this->reason,
        ];
    }
}
