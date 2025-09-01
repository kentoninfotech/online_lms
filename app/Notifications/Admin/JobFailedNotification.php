<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class JobFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**     
     * Create a new notification instance.
     */
    public function __construct(public string $title, public string $error) {}

    /**     
     * Get the notification's delivery channels.
     */
    public function via($notifiable) { return ['mail','database']; }

    /**     
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('❌ Zoom Sync Job Failed')
            ->line($this->title)
            ->line("Error: {$this->error}")
            ->line('Please investigate.');
    }

    /**     
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return ['message' => $this->title, 'error' => $this->error];
    }
}
