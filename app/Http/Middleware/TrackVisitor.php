<?php

namespace App\Http\Middleware;

use App\Models\VisitorLog;
use App\Services\GeoIp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Track public page visits:
     * - Increments total page views (cache, persisted to DB row).
     * - Records unique visitor per session.
     * - Marks visitor as "online" for 5 minutes (cache).
     * - Looks up country via GeoIp service.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track normal page GETs (skip Livewire updates, assets, ajax).
        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        try {
            $sessionId = $request->session()->getId();
            $ip = $request->ip() ?? '0.0.0.0';

            // Mark online (5-min sliding window).
            Cache::put('online.'.$sessionId, true, now()->addMinutes(5));

            // Always increment total page views.
            Cache::increment('stats.total_views');

            // Record unique visitor per session (1 record per session).
            $alreadyLogged = Cache::has('tracked.'.$sessionId);
            if (! $alreadyLogged) {
                $geo = GeoIp::lookup($ip);

                VisitorLog::create([
                    'session_id' => $sessionId,
                    'ip' => $ip,
                    'country' => $geo['country'] ?? null,
                    'country_code' => $geo['country_code'] ?? null,
                    'path' => substr($request->path(), 0, 500),
                    'user_agent' => substr((string) $request->userAgent(), 0, 500),
                ]);

                Cache::increment('stats.total_visitors');
                Cache::put('tracked.'.$sessionId, true, now()->addHours(2));
            }
        } catch (\Throwable $e) {
            // Never break a request because of tracking issues.
            report($e);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if (! $response->isSuccessful()) {
            return false;
        }

        // Skip Livewire internal endpoints and other non-page traffic.
        $path = $request->path();
        if (str_starts_with($path, 'livewire')
            || str_starts_with($path, '_boost')
            || str_starts_with($path, 'storage')
            || str_starts_with($path, 'up')
        ) {
            return false;
        }

        // Skip authenticated panel routes (admin/guru) — focus on public traffic.
        if (str_starts_with($path, 'admin') || str_starts_with($path, 'guru')) {
            return false;
        }

        $accept = (string) $request->header('Accept', '');
        if (! str_contains($accept, 'text/html') && $accept !== '*/*' && $accept !== '') {
            return false;
        }

        return true;
    }
}
