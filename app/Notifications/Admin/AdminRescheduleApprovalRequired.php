<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RescheduleRequest;

class AdminRescheduleApprovalRequired extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public RescheduleRequest $request) {}

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
        $occurrence = $this->request->occurrence;
        $lessonId = $occurrence->lesson_id ?? 'N/A';

        return (new MailMessage)
            ->subject('Reschedule Request Requires Approval')
            ->line("A reschedule request was submitted for Lesson #{$lessonId}.")
            ->line("Requested by: {$this->request->requester?->name}")
            ->line("Proposed new time: {$this->request->proposed_start?->format('d M Y H:i')}")
            ->action('Review Request', route("admin.reschedules"))
            ->line('Please review and approve/reject.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        $occurrence = $this->request->occurrence;
        $lessonId = $occurrence->lesson_id ?? 'N/A';
        $newTime = $this->request->proposed_start?->format('d M Y H:i');
        $requesterName = $this->request->requester?->name ?? 'Parent';

        $actionRoute = [
            'name' => 'admin.reschedules', 
            'params' => [], 
        ];

        return [
            'category' => 'Admin Tasks', 
            'request_id' => $this->request->id,
            'status'     => 'pending_admin_approval',

            'title' => 'Reschedule Request Needs Action',
            'message_lines' => [
                "A new reschedule request requires your approval for Lesson **#{$lessonId}**.",
                "Requested by: **{$requesterName}**",
                "Proposed new time: **{$newTime}**",
                'Please review the request details and take immediate action.',
            ],
            'action' => [
                'text' => 'Review Request',
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
            'request_id' => $this->request->id,
            'lesson_id' => $this->request->occurrence?->lesson_id,
            'proposed_start' => $this->request->proposed_start,
            'requested_by' => $this->request->requester->id,
        ];
    }
}
