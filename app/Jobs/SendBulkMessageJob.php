<?php

namespace App\Jobs;

use App\Models\BulkMessage;
use App\Models\BulkMessageRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Notifications\BulkMessageNotification;
use Illuminate\Support\Facades\Notification;

class SendBulkMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $bulkMessageId;
    public $tries = 3;

    public function __construct(int $bulkMessageId)
    {
        $this->bulkMessageId = $bulkMessageId;
    }

    public function handle(): void
    {
        $bulkMessage = BulkMessage::with('recipients.user')->find($this->bulkMessageId);

        if (! $bulkMessage) {
            Log::error("Bulk message record {$this->bulkMessageId} not found.");
            return;
        }

        // Mark as processing
        $bulkMessage->update(['status' => 'processing']);

        BulkMessageRecipient::where('bulk_message_id', $this->bulkMessageId)
            ->where('delivery_status', 'queued')
            ->orderBy('id')
            ->chunkById(50, function ($recipients) use ($bulkMessage) {
                foreach ($recipients as $recipient) {
                    try {
                        $user = $recipient->user;

                        if (! $user) {
                            $recipient->update([
                                'delivery_status' => 'failed',
                                'response_log'    => 'User not found',
                            ]);
                            continue;
                        }

                        // Convert delivery methods to array
                        $methods = is_array($bulkMessage->methods)
                            ? $bulkMessage->methods
                            : explode(',', $bulkMessage->methods);

                        // Send notification through selected channels
                        Notification::send(
                            $user,
                            new BulkMessageNotification(
                                $bulkMessage->subject,
                                $bulkMessage->message,
                                $methods
                            )
                        );

                        $recipient->update([
                            'delivery_status' => 'sent',
                            'response_log'    => 'Message sent successfully',
                        ]);

                    } catch (\Throwable $e) {
                        Log::error("SendBulkMessageJob failed for bulk {$this->bulkMessageId}: " . $e->getMessage());

                        $recipient->update([
                            'delivery_status' => 'failed',
                            'response_log'    => $e->getMessage(),
                        ]);
                    }
                }
            });

        // Mark bulk message as completed
        $bulkMessage->update(['status' => 'completed']);
    }

    public function failed(\Throwable $exception)
    {
        Log::error("SendBulkMessageJob failed for bulk {$this->bulkMessageId}: " . $exception->getMessage());

        // Mark all queued recipients as failed
        BulkMessageRecipient::where('bulk_message_id', $this->bulkMessageId)
            ->where('delivery_status', 'queued')
            ->update([
                'delivery_status' => 'failed',
                'response_log'    => 'Job failed: ' . $exception->getMessage(),
            ]);

        BulkMessage::where('id', $this->bulkMessageId)->update(['status' => 'failed']);
    }
}
