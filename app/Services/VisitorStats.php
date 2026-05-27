<?php

namespace App\Services;

use App\Models\VisitorLog;
use Illuminate\Support\Facades\Cache;

class VisitorStats
{
    /**
     * Aggregate dashboard for the public footer.
     *
     * @return array{
     *     total_views: int,
     *     total_visitors: int,
     *     today: int,
     *     online: int,
     *     top_countries: array<int, array{country: string, country_code: string, total: int}>
     * }
     */
    public static function summary(): array
    {
        return Cache::remember('visitor_stats.summary', now()->addMinutes(2), function () {
            // Total views: cache counter + fallback to DB count.
            $cachedViews = (int) Cache::get('stats.total_views', 0);
            $totalViews = max($cachedViews, VisitorLog::count());

            // Total unique visitors: count of session_id rows.
            $cachedVisitors = (int) Cache::get('stats.total_visitors', 0);
            $totalVisitors = max($cachedVisitors, VisitorLog::distinct('session_id')->count('session_id'));

            $today = VisitorLog::whereDate('created_at', today())->distinct('session_id')->count('session_id');

            // Online now — count unexpired session keys.
            $online = self::onlineCount();

            $topCountries = VisitorLog::query()
                ->selectRaw('country, country_code, COUNT(DISTINCT session_id) as total')
                ->whereNotNull('country')
                ->where('country_code', '!=', 'XX')
                ->groupBy('country', 'country_code')
                ->orderByDesc('total')
                ->limit(5)
                ->get()
                ->map(fn ($row) => [
                    'country' => (string) $row->country,
                    'country_code' => (string) $row->country_code,
                    'total' => (int) $row->total,
                ])
                ->all();

            return [
                'total_views' => $totalViews,
                'total_visitors' => $totalVisitors,
                'today' => $today,
                'online' => $online,
                'top_countries' => $topCountries,
            ];
        });
    }

    public static function onlineCount(): int
    {
        // The cache store may not support listing keys — do a best-effort scan.
        $store = Cache::getStore();
        if (method_exists($store, 'getRedis')) {
            try {
                $prefix = $store->getPrefix();
                $keys = $store->getRedis()->keys($prefix.'online.*');

                return is_array($keys) ? count($keys) : 0;
            } catch (\Throwable) {
                // Fall through.
            }
        }

        // Fallback: count distinct sessions seen in the last 5 minutes.
        return VisitorLog::where('created_at', '>=', now()->subMinutes(5))
            ->distinct('session_id')
            ->count('session_id');
    }
}
