<?php

namespace App\Notifications\Parent;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Subscription;

class BillingOverdue extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Subscription $subscription, public int $daysLeft) {}

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
        $end = $this->subscription->end_date->format('d M Y');
        $graceEnd = $this->subscription->end_date->copy()->addDays($this->daysLeft)->format('d M Y');

        return (new MailMessage)
            ->subject('Your Subscription is Overdue')
            ->line("Your subscription expired on {$end}.")
            ->line("You have until {$graceEnd} ({$this->daysLeft} days left) to renew before suspension.")
            ->action('Renew Now', url('/subscriptions/'.$this->subscription->id.'/renew'))
            ->line('Please make payment as soon as possible to avoid losing access.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'subscription_id' => $this->subscription->id,
            'message' => "Subscription expired. {$this->daysLeft} days left in grace period.",
        ];
    }
}
