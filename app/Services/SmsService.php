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
        $this->apiKey  = config('services.sms.api_key');
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
     * Normalize a phone number to E.164 format.
     */
    protected function normalizeNumber(string $number): ?string
    {
        // Clean number: remove spaces, (), -, dots, slashes
        $clean = preg_replace('/[^\d\+]/', '', $number);

        // Load world dialing codes
        $prefixes = config('sms.country_prefixes');
        $defaultIso = config('sms.default_country');
        $defaultDialing = config('sms.default_dialing_code');

        // If number starts with 00, convert to +
        if (str_starts_with($clean, '00')) {
            $clean = '+' . substr($clean, 2);
        }

        // If number begins with "+"
        if (str_starts_with($clean, '+')) {

            // Remove '+' for prefix matching
            $digits = substr($clean, 1);

            // Match longest possible prefix (max length 3 for E.164)
            for ($len = 3; $len >= 1; $len--) {
                $prefix = substr($digits, 0, $len);

                if (isset($prefixes[$prefix])) {
                    // Valid international number → return as-is
                    return '+' . $digits;
                }
            }

            // Starts with + but invalid → reject
            return null;
        }

        // For raw digit numbers without +
        // Attempt world prefix detection
        for ($len = 3; $len >= 1; $len--) {
            $prefix = substr($clean, 0, $len);

            if (isset($prefixes[$prefix])) {
                // Country detected → add "+"
                return '+' . $clean;
            }
        }

        // If no prefix matched → apply default (Nigeria +234)
        // Remove leading zero if present
        if (str_starts_with($clean, '0')) {
            $clean = substr($clean, 1);
        }

        return $defaultDialing . $clean;
    }


}
