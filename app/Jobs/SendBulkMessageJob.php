<?php

namespace App\Jobs;

use App\Models\BulkMessage;
use App\Models\BulkMessageRecipient;
use App\Notifications\BulkMessageNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendBulkMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $bulkMessageId;
    public int $tries = 3;

    public function __construct(int $bulkMessageId)
    {
        $this->bulkMessageId = $bulkMessageId;
    }

    public function handle(): void
    {
        $bulkMessage = BulkMessage::with('recipients.user')->find($this->bulkMessageId);

        if (!$bulkMessage) {
            Log::error("Bulk message {$this->bulkMessageId} not found.");
            return;
        }

        $bulkMessage->update(['status' => 'processing']);

        $failedCount = 0;
        $sentCount = 0;

        BulkMessageRecipient::where('bulk_message_id', $this->bulkMessageId)
            ->where('delivery_status', 'queued')
            ->orderBy('id')
            ->chunkById(50, function ($recipients) use ($bulkMessage, &$failedCount, &$sentCount) {
                foreach ($recipients as $recipient) {
                    $user = $recipient->user;

                    if (!$user) {
                        $recipient->update([
                            'delivery_status' => 'failed',
                            'response_log' => 'User not found',
                        ]);
                        $failedCount++;
                        continue;
                    }

                    try {
                        $methods = is_array($bulkMessage->methods)
                            ? $bulkMessage->methods
                            : explode(',', $bulkMessage->methods);

                        $methods = array_map(fn($m) => strtolower(trim($m)) === 'email' ? 'mail' : strtolower(trim($m)), $methods);

                        $notification = new BulkMessageNotification(
                            $bulkMessage->subject,
                            $bulkMessage->message,
                            $methods
                        );

                        Notification::send($user, $notification);

                        $logs = [];
                        if (in_array('mail', $methods)) $logs['mail'] = 'attempted';
                        if (in_array('sms', $methods)) $logs['sms'] = 'attempted';
                        $logs['database'] = 'stored';

                        $recipient->update([
                            'delivery_status' => 'sent',
                            'response_log' => json_encode($logs),
                        ]);

                        $sentCount++;
                    } catch (\Throwable $e) {
                        Log::error("SendBulkMessageJob failed for user {$user->id} in bulk {$this->bulkMessageId}: {$e->getMessage()}");

                        $recipient->update([
                            'delivery_status' => 'failed',
                            'response_log' => $e->getMessage(),
                        ]);

                        $failedCount++;
                    }
                }
            });

        $total = BulkMessageRecipient::where('bulk_message_id', $this->bulkMessageId)->count();

        if ($failedCount === 0) {
            $finalStatus = 'completed';
        } elseif ($failedCount === $total) {
            $finalStatus = 'failed';
        } else {
            $finalStatus = 'partial';
        }

        $bulkMessage->update(['status' => $finalStatus]);

        Log::info("BulkMessage {$this->bulkMessageId} completed with {$sentCount} sent, {$failedCount} failed. Final status: {$finalStatus}");
    }

    public function failed(\Throwable $exception)
    {
        Log::error("SendBulkMessageJob completely failed for bulk {$this->bulkMessageId}: {$exception->getMessage()}");

        BulkMessageRecipient::where('bulk_message_id', $this->bulkMessageId)
            ->where('delivery_status', 'queued')
            ->update([
                'delivery_status' => 'failed',
                'response_log' => 'Job failed: ' . $exception->getMessage(),
            ]);

        BulkMessage::where('id', $this->bulkMessageId)->update(['status' => 'failed']);
    }
}
