<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\WebsiteVisit;
use Jenssegers\Agent\Agent;

class TrackWebsiteVisit
{
    protected $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only track GET requests (not POST, PUT, DELETE, etc.)
        if ($request->isMethod('get') && !$this->shouldSkipTracking($request)) {
            try {
                $this->trackVisit($request);
            } catch (\Exception $e) {
                // Silently fail to not disrupt user experience
                \Log::error('Website visit tracking failed: ' . $e->getMessage());
            }
        }

        return $next($request);
    }

    /**
     * Track the website visit
     */
    protected function trackVisit(Request $request): void
    {
        $sessionId = session()->getId();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        
        // Parse user agent
        $this->agent->setUserAgent($userAgent);
        
        $data = [
            'session_id' => $sessionId,
            'user_id' => auth()->id(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_type' => $this->getDeviceType(),
            'browser' => $this->agent->browser(),
            'platform' => $this->agent->platform(),
            'referrer' => $request->header('referer'),
            'landing_page' => $request->fullUrl(),
            'current_page' => $request->fullUrl(),
        ];

        // Track the visit asynchronously (or queue it if you have queues set up)
        WebsiteVisit::trackVisit($data);
    }

    /**
     * Determine if tracking should be skipped
     */
    protected function shouldSkipTracking(Request $request): bool
    {
        $path = $request->path();
        
        // Skip tracking for these paths
        $skipPaths = [
            'admin/*',
            'api/*',
            'telescope/*',
            'horizon/*',
            'health',
            '_debugbar/*',
            'storage/*',
            'vendor/*',
        ];

        foreach ($skipPaths as $skipPath) {
            if ($request->is($skipPath)) {
                return true;
            }
        }

        // Skip tracking for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return true;
        }

        // Skip tracking for bots (optional - comment out if you want to track bots)
        if ($this->agent->isRobot()) {
            return true;
        }

        return false;
    }

    /**
     * Get device type
     */
    protected function getDeviceType(): string
    {
        if ($this->agent->isPhone()) {
            return 'mobile';
        } elseif ($this->agent->isTablet()) {
            return 'tablet';
        } elseif ($this->agent->isDesktop()) {
            return 'desktop';
        }
        
        return 'unknown';
    }
}
