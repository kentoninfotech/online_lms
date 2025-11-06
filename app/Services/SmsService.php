<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $sender;

    public function __construct()
    {
        $this->baseUrl = config('services.sms.url');
        $this->apiKey  = config('services.sms.key');
        $this->sender  = config('services.sms.sender', 'TheVirtualAcademy');
    }

    /**
     * Send an SMS to a recipient.
     */
    public function sendSms(string $number, string $message): bool
    {
        $formatted = $this->normalizeNumber($number);

        if (!$formatted) {
            Log::warning("Invalid phone number skipped: {$number}");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->post($this->baseUrl, [
                'to'      => $number,
                'from'    => $this->sender,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("SMS sent to {$number}");
                return true;
            } else {
                Log::error("SMS failed to {$number}: " . $response->body());
                return false;
            }
        } catch (Exception $e) {
            Log::error("SMS sending error for {$number}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Normalize phone number to E.164 format (e.g. 0809… → +234809…)
     */
    protected function normalizeNumber(string $number): ?string
    {
        $number = preg_replace('/\D/', '', $number);

        if (str_starts_with($number, '0')) {
            $number = '+234' . substr($number, 1);
        } elseif (!str_starts_with($number, '+')) {
            $number = '+' . $number;
        }

        return strlen($number) >= 10 ? $number : null;
    }
}
