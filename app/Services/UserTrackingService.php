<?php

namespace App\Services;

use App\Events\UserActivityTracked;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class UserTrackingService
{
    protected $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    /**
     * Track user event asynchronously
     */
    public function track(string $eventType, array $additionalData = []): void
    {
        try {
            $eventData = array_merge([
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'event_type' => $eventType,
                'page_url' => request()->fullUrl(),
                'page_title' => $additionalData['page_title'] ?? null,
                'referrer' => request()->header('referer'),
                'user_agent' => request()->userAgent(),
                'device_type' => $this->getDeviceType(),
                'browser' => $this->agent->browser(),
                'platform' => $this->agent->platform(),
                'ip_address' => request()->ip(),
            ], $additionalData);

            // Save to ShopAnalytics if shop-related event
            if (isset($additionalData['shop_id']) && in_array($eventType, ['contact_click', 'shop_view'])) {
                \App\Models\ShopAnalytics::track(
                    $additionalData['shop_id'],
                    $additionalData['event_action'] ?? $eventType,
                    Auth::id(),
                    [
                        'source' => $additionalData['event_action'] ?? $eventType,
                        'device_type' => $this->getDeviceType(),
                        'action_type' => $additionalData['action_type'] ?? null
                    ]
                );
            }

            // Dispatch event to queue
            event(new UserActivityTracked($eventData));
        } catch (\Exception $e) {
            // Silently fail to not disrupt user experience
            \Log::error('Tracking failed: ' . $e->getMessage());
        }
    }

    /**
     * Track page view
     */
    public function trackPageView(string $pageTitle, array $additionalData = []): void
    {
        $this->track('page_view', array_merge([
            'event_category' => 'navigation',
            'event_action' => 'viewed_page',
            'page_title' => $pageTitle,
        ], $additionalData));
    }

    /**
     * Track search
     */
    public function trackSearch(string $query, string $type = 'general', array $additionalData = []): void
    {
        $this->track('search', array_merge([
            'event_category' => 'search',
            'event_action' => 'performed_search',
            'event_label' => $query,
            'event_data' => [
                'query' => $query,
                'type' => $type,
            ],
        ], $additionalData));
    }

    /**
     * Track user interaction (click, scroll, etc.)
     */
    public function trackInteraction(string $action, string $label = null, array $additionalData = []): void
    {
        $this->track('interaction', array_merge([
            'event_category' => 'interaction',
            'event_action' => $action,
            'event_label' => $label,
        ], $additionalData));
    }

    /**
     * Track error
     */
    public function trackError(string $errorType, string $errorMessage, array $additionalData = []): void
    {
        $this->track('error', array_merge([
            'event_category' => 'error',
            'event_action' => $errorType,
            'event_label' => $errorMessage,
            'event_data' => [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
            ],
        ], $additionalData));
    }

    /**
     * Track shop view
     */
    public function trackShopView(int $shopId, string $shopName, int $cityId = null): void
    {
        $this->track('shop_view', [
            'event_category' => 'navigation',
            'event_action' => 'viewed_shop',
            'event_label' => $shopName,
            'shop_id' => $shopId,
            'city_id' => $cityId,
            'page_title' => $shopName,
        ]);
    }

    /**
     * Track city view
     */
    public function trackCityView(int $cityId, string $cityName): void
    {
        $this->track('city_view', [
            'event_category' => 'navigation',
            'event_action' => 'viewed_city',
            'event_label' => $cityName,
            'city_id' => $cityId,
            'page_title' => $cityName,
        ]);
    }

    /**
     * Track category view
     */
    public function trackCategoryView(int $categoryId, string $categoryName, int $cityId = null): void
    {
        $this->track('category_view', [
            'event_category' => 'navigation',
            'event_action' => 'viewed_category',
            'event_label' => $categoryName,
            'category_id' => $categoryId,
            'city_id' => $cityId,
            'page_title' => $categoryName,
        ]);
    }

    /**
     * Track conversion (phone call, direction click, etc.)
     */
    public function trackConversion(string $conversionType, string $label = null, array $additionalData = []): void
    {
        $this->track('conversion', array_merge([
            'event_category' => 'conversion',
            'event_action' => $conversionType,
            'event_label' => $label,
        ], $additionalData));
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
