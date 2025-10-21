<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payment;

class PaymentSubmitted extends Notification
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
            ->subject('New Payment Evidence Submitted')
            ->greeting('Hello Admin,')
            ->line("A new payment evidence has been submitted by parent: {$this->payment->parent->name}")
            ->line("Amount: ₦{$this->payment->amount}")
            ->action('Review Payment', route('payments.show', $this->payment)) 
            ->line('Please log in to approve or reject.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        $parentName = $this->payment->parent->name;
        $amount = number_format($this->payment->amount, 2);
        
        $actionRoute = [
            'name' => 'payments.show', 
            'params' => ['payment' => $this->payment->id], 
        ];

        return [
            'category' => 'Payments', 
            'payment_id' => $this->payment->id,
            'status'     => 'pending_review',
            'parent_name' => $parentName,

            'title' => 'New Payment Requires Review',
            'message_lines' => [
                'Hello Admin,',
                "A new payment evidence of **₦{$amount}** has been submitted by parent **{$parentName}**.",
                'Please log in to review the evidence and approve or reject the payment.',
            ],
            'action' => [
                'text' => 'Review Payment',
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
            'parent'     => $this->payment->parent->name,
            'amount'     => $this->payment->amount,
            'status'     => $this->payment->status,
        ];
    }
}
