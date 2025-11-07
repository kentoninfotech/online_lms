<?php

namespace App\Services;

use App\Models\User;
use App\Models\BulkMessage;
use App\Models\BulkMessageRecipient;
use App\Jobs\SendBulkMessageJob;

class BulkMessageService
{
    public function dispatchBulkMessage(string $subject, string $message, array $methods, array $recipientIds)
    {
        // Create bulk message record
        $bulkMessage = BulkMessage::create([
            'subject' => $subject,
            'message' => $message,
            'methods' => $methods,
            'status'  => 'queued',
            'sender'  => auth()->id(),
        ]);

        $recipients = User::with(['parent', 'student', 'instructor'])
            ->whereIn('id', $recipientIds)
            ->get();

        // Log each recipient
        foreach ($recipients as $user) {
            BulkMessageRecipient::create([
                'bulk_message_id'  => $bulkMessage->id,
                'user_id'          => $user->id,
                'email'            => $user->email,
                'number'           => $user->parent->number ?? $user->student->number ?? $user->instructor->number ?? null,
                'delivery_status'  => 'queued',
                'delivery_method'  => $methods,
            ]);
        }

        // Dispatch queued job
        SendBulkMessageJob::dispatch($bulkMessage->id);
    }
}
