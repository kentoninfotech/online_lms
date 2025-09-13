<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\LessonOccurrence;

class ClassReminder extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public LessonOccurrence $occurrence) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $lesson = $this->occurrence->lesson;
        $start = $this->occurrence->scheduled_start->format('d M Y H:i');

        return (new MailMessage)
            ->subject('Upcoming Class Reminder')
            ->line("This is a reminder for your upcoming lesson: {$lesson->subject}")
            ->line("Student: {$lesson->student->name}")
            ->line("Instructor: {$lesson->instructor->name}")
            ->line("Start Time: {$start}")
            ->action('Join Class', url("/lessons/{$lesson->id}"));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'lesson_id'     => $this->occurrence->lesson_id,
            'occurrence_id' => $this->occurrence->id,
            'start_time'    => $this->occurrence->scheduled_start,
            'message'       => "Upcoming lesson reminder",
        ];
    }
}
