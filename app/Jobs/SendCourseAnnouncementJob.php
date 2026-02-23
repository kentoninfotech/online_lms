<?php

namespace App\Jobs;

use App\Models\CourseBulkMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendCourseAnnouncementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public CourseBulkMessage $message) {}

    public function handle(): void
    {
        $this->message->load('recipients', 'course', 'sender');

        foreach ($this->message->recipients as $recipient) {
            try {
                // Send email if enabled
                if (in_array('email', $this->message->methods) && $recipient->email) {
                    Mail::send('emails.course-announcement', [
                        'recipient' => $recipient,
                        'message' => $this->message,
                    ], function ($mail) use ($recipient, $message) {
                        $mail->to($recipient->email)
                            ->subject($this->message->subject);
                    });
                }

                // Send SMS if enabled (implement your SMS service)
                if (in_array('sms', $this->message->methods) && $recipient->phone) {
                    // Call SMS service
                    // SmSService::send($recipient->phone, $this->message->message);
                }

                // Update recipient status
                $recipient->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                // Update sent count
                $this->message->increment('sent_count');

            } catch (\Exception $e) {
                Log::error('Failed to send course announcement', [
                    'message_id' => $this->message->id,
                    'recipient_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);

                $recipient->update([
                    'status' => 'failed',
                    'response' => $e->getMessage(),
                ]);
            }
        }
    }
}
