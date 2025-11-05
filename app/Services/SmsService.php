<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /*
    *  Send SMS using external SMS API
    */
    public function sendSms(string $number, string $message)
    {
        $number = $this->normalizePhone($number);

        try {
            $response = Http::post('https://api.smsprovider.com/send', [
                'to' => $number,
                'message' => $message,
                'from' => app('env.app_name', 'LMS'),
            ]);

            return $response->body();
        } catch (\Throwable $e) {
            Log::error("SMS sending failed to {$number}: " . $e->getMessage());
            throw $e;
        }
    }

    /*
    *  Normalize phone number to international format (e.g., 234XXXXXXXXXX for Nigeria)
    */
    public function normalizePhone(string $number): string
    {
        $number = preg_replace('/\D/', '', $number);
        if (str_starts_with($number, '0')) {
            return '234' . substr($number, 1);
        } elseif (!str_starts_with($number, '234')) {
            return '234' . $number;
        }
        return $number;
    }
}
