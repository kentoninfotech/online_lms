<?php

namespace App\Notifications\Admin;

use App\Models\ZoomSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WebhookFallbackDetected extends Notification implements ShouldQueue
{
    use Queueable;

    /**     
     * Create a new notification instance.
     */
    public function __construct(public ZoomSession $session) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('⚠ Zoom Webhook Fallback Triggered')
            ->line("Fallback mode synced participants for Zoom session #{$this->session->id}.")
            ->line("Zoom meeting id: {$this->session->zoom_meeting_id}")
            ->line('Please check the webhook delivery / subscription for Zoom.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        // $actionRoute = [
        //     'name' => 'admin.zoom.settings', // Assuming an admin route to check Zoom settings
        //     'params' => [],
        // ];

        return [
            'category' => 'Admin Tasks',
            'session_id' => $this->session->id,
            'zoom_meeting_id' => $this->session->zoom_meeting_id,
            'status'     => 'warning',

            'title' => 'Zoom Webhook Fallback Triggered ⚠️',
            'message_lines' => [
                'Fallback mode was triggered to sync participants for a lesson.',
                "Session ID: #{$this->session->id}",
                "Zoom Meeting ID: {$this->session->zoom_meeting_id}",
                'This suggests the primary Zoom webhook is failing. Please check the webhook delivery and subscription for errors.',
            ],
            'action' => [
                'text' => 'Check Zoom Settings',
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
            'message' => "Fallback sync performed for Zoom session {$this->session->id}",
            'zoom_meeting_id' => $this->session->zoom_meeting_id,
        ];
    }
}
