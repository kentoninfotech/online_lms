<?php

namespace App\Jobs;

use App\Models\BulkMessage;
use App\Models\BulkMessageRecipient;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBulkMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recipients;
    public $subject;
    public $message;
    public $methods;
    public $bulkMessageId;

    /**
     * Create a new job instance.
     */
    public function __construct($recipients, $subject, $message, $methods, $bulkMessageId)
    {
        $this->recipients = $recipients;
        $this->subject = $subject;
        $this->message = $message;
        $this->methods = $methods;
        $this->bulkMessageId = $bulkMessageId;
    }

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService)
    {
        $bulkMessage = BulkMessage::find($this->bulkMessageId);

        if (!$bulkMessage) {
            Log::error("Bulk message record {$this->bulkMessageId} not found.");
            return;
        }

        foreach ($this->recipients as $user) {
            $emailStatus = 'skipped';
            $smsStatus = 'skipped';
            $errorMessage = null;

            try {
                // EMAIL
                if (in_array('email', $this->methods) && $user->email) {
                    Mail::raw($this->message, function ($mail) use ($user) {
                        $mail->to($user->email)->subject($this->subject);
                    });
                    $emailStatus = 'sent';
                }

                // SMS
                if (in_array('sms', $this->methods)) {
                    $number = $user->parent->number
                        ?? $user->student->number
                        ?? $user->instructor->number
                        ?? null;

                    if ($number) {
                        $smsService->sendSms($number, $this->message);
                        $smsStatus = 'sent';
                    }
                }

                // Update recipient status
                BulkMessageRecipient::where('bulk_message_id', $this->bulkMessageId)
                    ->where('user_id', $user->id)
                    ->update([
                        'status' => 'sent',
                        'email_status' => $emailStatus,
                        'sms_status' => $smsStatus,
                        'error' => null,
                    ]);

                Log::info("Message sent to user ID {$user->id}");
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();

                BulkMessageRecipient::where('bulk_message_id', $this->bulkMessageId)
                    ->where('user_id', $user->id)
                    ->update([
                        'status' => 'failed',
                        'email_status' => $emailStatus,
                        'sms_status' => $smsStatus,
                        'error' => $errorMessage,
                    ]);

                Log::error("Bulk message failed for user ID {$user->id}: {$errorMessage}");
            }
        }

        // Mark bulk message as completed after all
        $bulkMessage->update(['status' => 'completed']);
    }
}
