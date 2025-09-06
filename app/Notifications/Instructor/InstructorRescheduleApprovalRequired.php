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
        return (new MailMessage)
            ->subject('Reschedule Request From Your Student')
            ->line("Your student has requested to reschedule Lesson #{$this->request->occurrence->lesson_id}.")
            ->line("Old time: {$this->request->occurrence->scheduled_start->format('d M Y H:i')}")
            ->line("Proposed new time: {$this->request->proposed_start->format('d M Y H:i')}")
            ->action('Review Request', url("/instructor/reschedules/{$this->request->id}"))
            ->line('Please confirm if this new time works for you.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'request_id' => $this->request->id,
            'lesson_id' => $this->request->occurrence->lesson_id,
            'proposed_start' => $this->request->proposed_start,
            'student_id' => $this->request->occurrence->lesson->student_id,
        ];
    }
}
