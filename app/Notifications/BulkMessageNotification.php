<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Support\Facades\Log;

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
        // Always normalize “email” → “mail”
        $this->methods = array_map(fn($m) => $m === 'email' ? 'mail' : $m, $methods);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        $channels = ['database'];

        if (in_array('mail', $this->methods)) {
            $channels[] = 'mail';
        }

        if (in_array('sms', $this->methods)) {
            $channels[] = SmsChannel::class;
        }

        Log::info("Notification via() resolved channels", ['channels' => $channels]);

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        \Log::info('Email channel triggered for user: ' . ($notifiable->email ?? 'N/A'));

        return (new MailMessage)
            ->subject($this->subject)
            ->greeting('Hello ' . ($notifiable->name ?? ''))
            ->line($this->message)
            ->salutation('Kind regards, ' . config('app.name'));
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms($notifiable)
    {
        \Log::info('SMS channel triggered', [
            'methods' => $this->methods,
            'user' => $notifiable->phone_number ?? 'N/A'
        ]);

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

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'subject' => $this->subject,
            'message' => $this->message,
            'methods' => $this->methods,
        ];
    }
}
