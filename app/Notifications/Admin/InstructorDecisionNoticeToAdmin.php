<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RescheduleRequest;

class InstructorDecisionNoticeToAdmin extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public RescheduleRequest $request, 
        public string $decision,  // "approved" | "rejected"
        public string $actorName  // instructor name
    ) {}

    // Get the notification's delivery channels.
    public function via($notifiable) { return ['mail','database']; }

    // Get the mail representation of the notification.
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject("Instructor {$this->decision} a Reschedule")
            ->greeting('Hello Admin,')
            ->line("Instructor **{$this->actorName}** has {$this->decision} a reschedule request.")
            ->line("Occurrence ID: #{$this->request->lesson_occurrence_id}")
            ->line("Original: ".$this->request->occurrence->scheduled_start->format('d M Y H:i'))
            ->line("Proposed: ".$this->request->proposed_start->format('d M Y H:i'))
            ->line("Student/Parent Reason: {$this->request->reason}");

        if (!empty($this->request->decision_reason)) {
            $mail->line("Decision Reason: {$this->request->decision_reason}");
        }

        return $mail->line('This is a heads-up so you’re aware of the updated schedule.');
    }

    // Get the array representation of the notification.
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
