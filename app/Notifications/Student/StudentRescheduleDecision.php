<?php

namespace App\Notifications\Student;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RescheduleRequest;

class StudentRescheduleDecision extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public RescheduleRequest $request, public string $decision) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable) { return ['mail','database']; }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject("Your Lesson Reschedule was {$this->decision}")
            ->line("Lesson #{$this->request->occurrence->lesson_id} reschedule request has been {$this->decision}.")
            ->line("Requested reason: {$this->request->reason}")
            ->line("Proposed new time: {$this->request->proposed_start->format('d M Y H:i')}");

        if ($this->decision === 'rejected' && $this->request->decision_reason) {
            $mail->line("Rejection reason: {$this->request->decision_reason}");
        }

        return $mail;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        $newTime = $this->request->proposed_start->format('d M Y H:i');
        $isApproved = strtolower($this->decision) === 'approved';
        $title = "Reschedule Request {$this->decision}";
        $lessonId = $this->request->occurrence->lesson_id;
        
        $messageLines = [
            "Your reschedule request for Lesson **#{$lessonId}** has been **{$this->decision}**.",
            "Requested reason: {$this->request->reason}",
        ];
        
        if ($isApproved) {
            $messageLines[] = "The lesson is now confirmed for: **{$newTime}**.";
            $messageLines[] = "Be prepared for your class at the new time!";
        } else {
            $messageLines[] = "Proposed new time: {$newTime}";
            if ($this->request->decision_reason) {
                $messageLines[] = "Rejection reason: {$this->request->decision_reason}";
            }
            $messageLines[] = "The original schedule is still active. Please contact support if you need assistance.";
        }
        
        $actionRoute = [
            'name' => 'student.lessons',
            'params' => [], 
        ];

        return [
            'category' => 'Reschedules', 
            'request_id' => $this->request->id,
            'status'     => $this->decision,

            'title' => $title,
            'message_lines' => $messageLines,
            'action' => [
                'text' => 'View Schedule',
                'route' => $actionRoute,
            ],
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'request_id'      => $this->request->id,
            'lesson_id'       => $this->request->occurrence->lesson_id,
            'status'          => $this->decision,
            'reason'          => $this->request->reason,
            'decision_reason' => $this->request->decision_reason,
            'new_time'        => $this->request->proposed_start,
        ];
    }
}
