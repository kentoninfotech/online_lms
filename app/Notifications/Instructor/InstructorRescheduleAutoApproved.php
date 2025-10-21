<?php

namespace App\Notifications\Instructor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RescheduleRequest;

class InstructorRescheduleAutoApproved extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public RescheduleRequest $request) {}

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
            ->subject('Lesson Reschedule Auto-Approved')
            ->greeting('Hello Instructor,')
            ->line("A reschedule request for your lesson occurrence #{$this->request->lesson_occurrence_id} was auto-approved.")
            ->line("Reason: {$this->request->reason}")
            ->line("New date/time: {$this->request->proposed_start->format('d M Y H:i')}")
            ->line('No manual action is required, but please note this updated schedule.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        $newTime = $this->request->proposed_start->format('d M Y H:i');

        $actionRoute = [
            'name' => 'instructor.reschedules', 
            'params' => [], 
        ];

        return [
            'category' => 'Classes', 
            'request_id' => $this->request->id,
            'status'     => 'auto_approved',

            'title' => 'Lesson Reschedule Auto-Approved',
            'message_lines' => [
                'Hello Instructor,',
                "A reschedule request for your lesson occurrence **#{$this->request->lesson_occurrence_id}** was auto-approved.",
                "Reason: {$this->request->reason}",
                "New date/time: **{$newTime}**.",
                'No manual action is required, but please note this updated schedule.',
            ],
            'action' => [
                'text' => 'View Schedule',
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
            'occurrence_id' => $this->request->lesson_occurrence_id,
            'proposed_start' => $this->request->proposed_start,
            'reason' => $this->request->reason,
        ];
    }
}
