<?php

namespace App\Notifications\Parent;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Subscription;

class SubscriptionExpiryWarning extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Subscription $subscription) {}

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
        $endDate = $this->subscription->end_date->format('d M Y');

        return (new MailMessage)
            ->subject('Your Subscription is Expiring Soon')
            ->line("Your subscription for plan **{$this->subscription->plan->name}** is expiring on **{$endDate}**.")
            ->line('Please renew to avoid interruption of your lessons.')
            ->action('Renew Subscription', url("/subscriptions/{$this->subscription->id}/renew"))
            ->line('Thank you for staying with us!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'subscription_id' => $this->subscription->id,
            'message'         => "Your subscription for {$this->subscription->plan->name} is expiring soon.",
        ];
    }
}
