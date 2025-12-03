<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WebsiteVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'referrer',
        'landing_page',
        'current_page',
        'country',
        'city',
        'is_unique_visit',
        'is_bounce',
        'pages_viewed',
        'duration_seconds',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'is_unique_visit' => 'boolean',
        'is_bounce' => 'boolean',
        'pages_viewed' => 'integer',
        'duration_seconds' => 'integer',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Track a new website visit
     */
    public static function trackVisit(array $data): self
    {
        $sessionId = $data['session_id'];
        $ipAddress = $data['ip_address'];
        $today = Carbon::today();

        // Check if this is a unique visit (first visit from this IP today)
        $isUniqueVisit = !self::where('ip_address', $ipAddress)
            ->whereDate('created_at', $today)
            ->exists();

        // Check if session already exists (within last 30 minutes)
        $existingVisit = self::where('session_id', $sessionId)
            ->where('created_at', '>=', Carbon::now()->subMinutes(30))
            ->first();

        if ($existingVisit) {
            // Update existing visit
            $existingVisit->update([
                'current_page' => $data['current_page'] ?? $existingVisit->current_page,
                'pages_viewed' => $existingVisit->pages_viewed + 1,
                'last_seen_at' => now(),
                'duration_seconds' => now()->diffInSeconds($existingVisit->first_seen_at),
                'is_bounce' => false, // More than one page viewed
            ]);

            return $existingVisit;
        }

        // Create new visit
        return self::create([
            'session_id' => $sessionId,
            'user_id' => $data['user_id'] ?? null,
            'ip_address' => $ipAddress,
            'user_agent' => $data['user_agent'] ?? null,
            'device_type' => $data['device_type'] ?? null,
            'browser' => $data['browser'] ?? null,
            'platform' => $data['platform'] ?? null,
            'referrer' => $data['referrer'] ?? null,
            'landing_page' => $data['landing_page'] ?? null,
            'current_page' => $data['current_page'] ?? null,
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'is_unique_visit' => $isUniqueVisit,
            'is_bounce' => true, // Assume bounce until proven otherwise
            'pages_viewed' => 1,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);
    }

    /**
     * Get statistics for a date range
     */
    public static function getStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $visits = self::whereBetween('created_at', [$startDate, $endDate])->get();

        return [
            'total_visits' => $visits->count(),
            'unique_visitors' => $visits->unique('ip_address')->count(),
            'unique_visits' => $visits->where('is_unique_visit', true)->count(),
            'returning_visitors' => $visits->where('is_unique_visit', false)->count(),
            'total_page_views' => $visits->sum('pages_viewed'),
            'avg_pages_per_visit' => $visits->count() > 0 ? round($visits->avg('pages_viewed'), 2) : 0,
            'avg_duration' => $visits->count() > 0 ? round($visits->avg('duration_seconds'), 2) : 0,
            'bounce_rate' => $visits->count() > 0 ? round(($visits->where('is_bounce', true)->count() / $visits->count()) * 100, 1) : 0,
            'mobile_visits' => $visits->where('device_type', 'mobile')->count(),
            'tablet_visits' => $visits->where('device_type', 'tablet')->count(),
            'desktop_visits' => $visits->where('device_type', 'desktop')->count(),
        ];
    }

    /**
     * Get daily visit statistics
     */
    public static function getDailyStats(int $days = 30): array
    {
        $stats = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            
            $dayVisits = self::whereDate('created_at', $date)->get();
            
            $stats[] = [
                'date' => $dateStr,
                'day_name' => $date->format('D'),
                'total_visits' => $dayVisits->count(),
                'unique_visitors' => $dayVisits->unique('ip_address')->count(),
                'page_views' => $dayVisits->sum('pages_viewed'),
                'avg_duration' => $dayVisits->count() > 0 ? round($dayVisits->avg('duration_seconds'), 2) : 0,
                'bounce_rate' => $dayVisits->count() > 0 ? round(($dayVisits->where('is_bounce', true)->count() / $dayVisits->count()) * 100, 2) : 0,
            ];
        }

        return $stats;
    }

    /**
     * Get hourly distribution
     */
    public static function getHourlyDistribution(int $days = 7): array
    {
        $distribution = [];
        
        for ($hour = 0; $hour < 24; $hour++) {
            $distribution[] = [
                'hour' => $hour,
                'visits' => self::where('created_at', '>=', Carbon::now()->subDays($days))
                    ->whereRaw('HOUR(created_at) = ?', [$hour])
                    ->count(),
            ];
        }

        return $distribution;
    }

    /**
     * Get top referrers
     */
    public static function getTopReferrers(int $days = 30, int $limit = 10): array
    {
        return self::select('referrer', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->groupBy('referrer')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get top landing pages
     */
    public static function getTopLandingPages(int $days = 30, int $limit = 10): array
    {
        return self::select('landing_page', DB::raw('COUNT(*) as visits'))
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->whereNotNull('landing_page')
            ->groupBy('landing_page')
            ->orderBy('visits', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get device breakdown
     */
    public static function getDeviceBreakdown(int $days = 30): array
    {
        return self::select('device_type', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->get()
            ->toArray();
    }

    /**
     * Get browser breakdown
     */
    public static function getBrowserBreakdown(int $days = 30, int $limit = 10): array
    {
        return self::select('browser', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get real-time visitors (last 5 minutes)
     */
    public static function getRealTimeVisitors(): int
    {
        return self::where('last_seen_at', '>=', Carbon::now()->subMinutes(5))
            ->distinct('session_id')
            ->count();
    }

    /**
     * Get visitor count for today
     */
    public static function getTodayVisitors(): int
    {
        return self::whereDate('created_at', Carbon::today())
            ->distinct('ip_address')
            ->count();
    }

    /**
     * Get comparison with previous period
     */
    public static function getComparison(int $days = 30): array
    {
        $currentStart = Carbon::now()->subDays($days);
        $currentEnd = Carbon::now();
        $previousStart = Carbon::now()->subDays($days * 2);
        $previousEnd = Carbon::now()->subDays($days);

        $currentStats = self::getStatistics($currentStart, $currentEnd);
        $previousStats = self::getStatistics($previousStart, $previousEnd);

        $comparison = [];
        foreach ($currentStats as $key => $value) {
            $previousValue = $previousStats[$key] ?? 0;
            $change = $previousValue > 0 ? (($value - $previousValue) / $previousValue) * 100 : 0;
            
            $comparison[$key] = [
                'current' => $value,
                'previous' => $previousValue,
                'change' => round($change, 2),
                'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable'),
            ];
        }

        return $comparison;
    }
}
