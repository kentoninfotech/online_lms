<?php

namespace App\Notifications\Instructor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RescheduleRequest;

class AdminDecisionNoticeToInstructor extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public RescheduleRequest $request, 
        public string $decision,   // "approved" | "rejected"
        public string $actorName   // admin name
    ){}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable) { return ['mail','database']; }

    /*
    * Get the mail representation of the notification.
    */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject("Admin {$this->decision} a Reschedule")
            ->greeting('Hello Instructor,')
            ->line("Admin **{$this->actorName}** has {$this->decision} a reschedule request for your class.")
            ->line("Occurrence ID: #{$this->request->lesson_occurrence_id}")
            ->line("Original: ".$this->request->occurrence->scheduled_start->format('d M Y H:i'))
            ->line("Proposed: ".$this->request->proposed_start->format('d M Y H:i'))
            ->line("Student/Parent Reason: {$this->request->reason}");

        if (!empty($this->request->decision_reason)) {
            $mail->line("Decision Reason: {$this->request->decision_reason}");
        }

        return $mail->line('Please review your calendar and prepare for the new time if applicable.');
    }

    /* 
    * Get the database representation of the notification.
    */
    public function toDatabase($notifiable)
    {
        $oldTime = $this->request->occurrence->scheduled_start->format('d M Y H:i');
        $newTime = $this->request->proposed_start->format('d M Y H:i');
        $isApproved = strtolower($this->decision) === 'approved';
        $title = "Reschedule {$this->decision} by Admin";

        $messageLines = [
            "Admin **{$this->actorName}** has **{$this->decision}** a reschedule request for your class.",
            "Occurrence ID: #{$this->request->lesson_occurrence_id}",
            "Original time: {$oldTime}",
            "Proposed time: {$newTime}",
            "Student/Parent Reason: {$this->request->reason}",
        ];

        if (!empty($this->request->decision_reason)) {
            $messageLines[] = "Decision Reason: {$this->request->decision_reason}";
        }

        $messageLines[] = $isApproved
            ? 'Please review your calendar and prepare for the new time.'
            : 'The schedule remains unchanged. Please review the decision details.';

        $actionRoute = [
            'name' => 'instructor.reschedules', 
            'params' => [], 
        ];

        return [
            'category' => 'Reschedules', 
            'request_id' => $this->request->id,
            'decision' => $this->decision,
            'actor' => $this->actorName,

            'title' => $title,
            'message_lines' => $messageLines,
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
            'request_id'      => $this->request->id,
            'occurrence_id'   => $this->request->lesson_occurrence_id,
            'decision'        => $this->decision,
            'proposed_start'  => $this->request->proposed_start,
            'reason'          => $this->request->reason,
            'decision_reason' => $this->request->decision_reason,
            'actor'           => $this->actorName,
        ];
    }
}
