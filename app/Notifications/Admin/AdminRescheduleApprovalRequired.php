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
            ->action('Review Request', url("/admin/reschedules/{$this->request->id}"))
            ->line('Please review and approve/reject.');
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
