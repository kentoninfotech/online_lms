<?php

namespace App\Notifications\Instructor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RescheduleRequest;

class InstructorRescheduleApprovalRequired extends Notification
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
            ->subject('Reschedule Request From Your Student')
            ->line("Your student has requested to reschedule Lesson #{$lessonId}.")
            ->line("Old time: {$this->request->occurrence->scheduled_start?->format('d M Y H:i')}")
            ->line("Proposed new time: {$this->request->proposed_start?->format('d M Y H:i')}")
            ->action('Review Request', route("instructor.reschedules"))
            ->line('Please confirm if this new time works for you.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        $occurrence = $this->request?->occurrence;
        $lessonId = $occurrence->lesson_id ?? 'N/A';
        $oldTime = $occurrence->scheduled_start?->format('d M Y H:i');
        $newTime = $this->request->proposed_start?->format('d M Y H:i');

        $actionRoute = [
            'name' => 'instructor.reschedules', 
            'params' => [], 
        ];

        return [
            'category' => 'Reschedules', 
            'request_id' => $this->request->id,
            'status'     => 'pending_approval',

            'title' => 'New Reschedule Request',
            'message_lines' => [
                'Hello Instructor,',
                "Your student has requested to reschedule Lesson **#{$lessonId}**.",
                "Old time: **{$oldTime}**",
                "Proposed new time: **{$newTime}**",
                'Please review and confirm if this new time works for you.',
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
            'student_id' => $this->request->occurrence->lesson->student_id,
        ];
    }
}
