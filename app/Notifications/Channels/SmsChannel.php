<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);

        if (empty($message)) {
            Log::warning('Empty SMS message, skipping.');
            return;
        }

        $number = $notifiable->routeNotificationFor('sms', $notification);

        if (! $number) {
            Log::warning("No phone number found for notifiable ID {$notifiable->id}");
            return;
        }

        $this->smsService->sendSms($number, $message);
    }
}
