<?php

namespace App\Services;

use App\Models\LessonOccurrence;
use App\Models\ZoomSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ZoomService
{
    private string $base;
    private string $auth;

    public function __construct()
    {
        $this->base = config('services.zoom.base_url');
        $this->auth = config('services.zoom.auth_url');
    }

    /**
     * Get / cache access token for S2S OAuth (account_credentials).
     */
    public function getAccessToken(): string
    {
        return Cache::remember('zoom_s2s_token', 3300, function () {
            $resp = Http::asForm()->withBasicAuth(
                config('services.zoom.client_id'), 
                config('services.zoom.client_secret')
            )->post($this->auth, [
                'grant_type' => 'account_credentials',
                'account_id' => config('services.zoom.account_id'),
            ]);

            if ($resp->failed()) {
                throw new \RuntimeException('Zoom token fetch failed: ' . $resp->body());
            }

            return $resp->json('access_token');
        });
    }

    /**
     * Shortcut for Http client with auto-refresh token.
     */
    private function client()
    {
        $base = $this->base;

        return Http::baseUrl($base)
            ->acceptJson()
            ->withToken($this->getAccessToken())
            ->tap(function ($pendingRequest) use ($base) {
                $pendingRequest->withMiddleware(function ($handler) use ($base) {
                    return function ($request, $options) use ($handler) {
                        return $handler($request, $options)->then(function ($response) use ($request, $handler, $options) {
                            if ($response->getStatusCode() === 401) {
                                // Token invalid/expired → refresh
                                Cache::forget('zoom_access_token');
                                $newToken = app(self::class)->getAccessToken();

                                $request = $request->withHeader('Authorization', "Bearer {$newToken}");
                                return $handler($request, $options);
                            }
                            return $response;
                        });
                    };
                });
            });
    }

    /**
     * Create meeting using host (zoom user id or "me")
     * Returns API response array.
     */
    public function createMeeting(LessonOccurrence $occ, ?string $hostZoomUserId = null): array
    {
        $start = $occ->scheduled_start->copy()->setTimezone(config('services.zoom.default_timezone'));

        $payload = [
            'topic' => $occ->lesson->subject . ' — ' . ($occ->lesson->student->name ?? 'Student'),
            'type' => 2, // scheduled
            'start_time' => $start->format('Y-m-d\TH:i:s'),
            'duration' => $occ->duration_minutes,
            'timezone' => config('services.zoom.default_timezone'),
            'settings' => [
                'waiting_room' => true,
                'join_before_host' => false,
                'mute_upon_entry' => true,
                'approval_type' => 2,
                'audio' => 'voip',
            ],
        ];

        // use host if provided else "me"
        $host = $hostZoomUserId ?: 'me';
        $resp = $this->client()->post("/users/{$host}/meetings", $payload);

        if ($resp->failed()) {
            throw new \RuntimeException('Zoom create meeting failed: ' . $resp->body());
        }

        $json = $resp->json();

        return [
            'id'        => (string) ($json['id'] ?? ''),
            'join_url'  => $json['join_url'] ?? null,
            'start_url' => $json['start_url'] ?? null,
            'password'  => $json['password'] ?? null,
            'raw'       => $json, // keep raw for debugging
        ];

    }

    /**
     * Delete meeting
     */
    public function deleteMeeting(string $meetingId): void
    {
        $resp = $this->client()->delete("/meetings/{$meetingId}");
        if ($resp->failed() && $resp->status() !== 404) {
            throw new \RuntimeException('Zoom delete meeting failed: ' . $resp->body());
        }
    }

    /**
     * List past participants for a meeting (paginated).
     * Returns array: ['participants' => [...], 'next_page_token' => '...']
     *
     * Note: for some Zoom plans you use /report/meetings/{meetingId}/participants
     * as implemented here. If your account uses a different endpoint, adjust.
     */
    public function listPastParticipants(string $meetingId, ?string $nextPageToken = null): array
    {
        $params = ['page_size' => 300];
        if ($nextPageToken) $params['next_page_token'] = $nextPageToken;

        // prefer /report endpoint (returns participants for past meetings)
        $resp = $this->client()->get("/report/meetings/{$meetingId}/participants", $params);

        // fallback to past_meetings if report endpoint not available
        if ($resp->status() === 404) {
            $resp = $this->client()->get("/past_meetings/{$meetingId}/participants", $params);
        }

        if ($resp->failed()) {
            throw new \RuntimeException('Zoom participants fetch failed: ' . $resp->body());
        }

        return $resp->json();
    }

    /**
     * Verify webhook payload. Zoom has multiple signature schemes; adapt if needed.
     *
     * This simple implementation:
     *  - reads webhook secret from config('services.zoom.webhook_secret')
     *  - computes HMAC SHA256 of raw body and compares with header 'x-zm-signature' or 'x-zoom-signature'
     *
     * IMPORTANT: If Zoom uses base64-encoded HMAC or another header, update this method accordingly.
     */
    public function verifyWebhook(string $rawBody, string $headerSignature = null): bool
    {
        $secret = config('services.zoom.webhook_secret');
        if (empty($secret)) {
            // no secret configured — do not verify (or you can return false to be strict)
            return true;
        }

        $candidates = [
            $headerSignature,
            // sometimes Zoom uses different header names. Your controller should pass the header it receives.
        ];

        // compute hex HMAC
        $computedHex = hash_hmac('sha256', $rawBody, $secret);
        // compute raw binary then base64 (some Zoom setups expect base64)
        $computedBase64 = base64_encode(hash_hmac('sha256', $rawBody, $secret, true));

        foreach ($candidates as $sig) {
            if (empty($sig)) continue;
            // strip possible "sha256=" prefix
            $sigClean = preg_replace('/^sha256=/', '', $sig);

            if (hash_equals($sigClean, $computedHex) || hash_equals($sigClean, $computedBase64)) {
                return true;
            }
        }

        return false;
    }
}

