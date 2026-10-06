<?php

namespace App\Http\Middleware;

use App\Models\SiteVisitor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$this->shouldTrack($request, $response)) {
            return $response;
        }

        $ipAddress = (string) $request->ip();
        $geoData = $this->resolveGeoData($ipAddress);

        SiteVisitor::create([
            'user_id' => auth()->id(),
            'ip_address' => $ipAddress,
            'country' => $geoData['country'],
            'location' => $geoData['location'],
            'visited_url' => $request->fullUrl(),
            'visited_at' => now(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (!$request->isMethod('GET')) {
            return false;
        }

        if ($request->expectsJson()) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        $path = trim($request->path(), '/');
        $excludedPrefixes = [
            'admin',
            'dashboard',
            'api',
            '_debugbar',
            'up',
            'storage',
            'assets',
            'build',
        ];

        foreach ($excludedPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return false;
            }
        }

        return true;
    }

    private function resolveGeoData(string $ipAddress): array
    {
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return [
                'country' => 'Local/Private Network',
                'location' => null,
            ];
        }

        try {
            $response = Http::timeout(3)
                ->get("http://ip-api.com/json/{$ipAddress}", [
                    'fields' => 'status,country,city,regionName,message',
                ]);

            if (!$response->ok()) {
                \Log::warning('Visitor geolocation lookup failed', [
                    'ip' => $ipAddress,
                    'http_status' => $response->status(),
                ]);

                return [
                    'country' => null,
                    'location' => null,
                ];
            }

            $payload = $response->json();

            if (($payload['status'] ?? null) !== 'success') {
                \Log::warning('Visitor geolocation lookup returned failure', [
                    'ip' => $ipAddress,
                    'message' => $payload['message'] ?? 'unknown',
                ]);

                return [
                    'country' => null,
                    'location' => null,
                ];
            }

            $locationParts = array_filter([
                $payload['city'] ?? null,
                $payload['regionName'] ?? null,
            ]);

            return [
                'country' => $payload['country'] ?? null,
                'location' => !empty($locationParts) ? implode(', ', $locationParts) : null,
            ];
        } catch (\Throwable $exception) {
            \Log::warning('Visitor geolocation request threw an exception', [
                'ip' => $ipAddress,
                'error' => $exception->getMessage(),
            ]);

            return [
                'country' => null,
                'location' => null,
            ];
        }
    }
}
