<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\SmsChannel;

class BulkMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subject;
    protected $message;
    protected $methods;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $subject, string $message, array $methods)
    {
        $this->subject     = $subject;
        $this->message     = $message;
        $this->methods     = $methods;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        $channels = ['database']; // always log

        if (in_array('email', $this->methods) && !empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        if (in_array('sms', $this->methods) && !empty($notifiable->phone_number)) {
            $channels[] = SmsChannel::class;
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting('Hello ' . ($notifiable->name ?? ''))
            ->line($this->message)
            ->salutation('— TheVirtualAcademy Admin');
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms($notifiable)
    {
        return $this->message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'category'     => 'Messages',
            'title'        => $this->subject,
            'message'      => $this->message,
            'methods'      => $this->methods,
            'email'        => $notifiable->email ?? null,
            'phone_number' => $notifiable->phone_number ?? null,
            'status'       => 'sent',
        ];
    }
}
