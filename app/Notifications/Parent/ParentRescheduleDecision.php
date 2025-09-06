<?php

namespace App\Notifications\Parent;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ParentRescheduleDecision extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public RescheduleRequest $request, public string $decision) {}

    public function via($notifiable) { return ['mail','database']; }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject("Your Reschedule Request was {$this->decision}")
            ->line("Lesson #{$this->request->occurrence->lesson_id} reschedule request has been {$this->decision}.")
            ->line("Requested reason: {$this->request->reason}")
            ->line("Proposed new time: {$this->request->proposed_start->format('d M Y H:i')}");

        if ($this->decision === 'rejected' && $this->request->decision_reason) {
            $mail->line("Rejection reason: {$this->request->decision_reason}");
        }

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            'request_id'       => $this->request->id,
            'lesson_id'        => $this->request->occurrence->lesson_id,
            'status'           => $this->decision,
            'reason'           => $this->request->reason,
            'decision_reason'  => $this->request->decision_reason,
            'new_time'         => $this->request->proposed_start,
        ];
    }
}
