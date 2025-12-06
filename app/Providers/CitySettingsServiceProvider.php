<?php

namespace App\Providers;

use App\Models\City;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class CitySettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share current city settings with all views
        View::composer('*', function ($view) {
            $currentCity = $this->getCurrentCity();
            
            // Get site-wide defaults
            $siteName = \App\Models\AppSetting::get('site_name', 'City App');
            $siteNameAr = \App\Models\AppSetting::get('site_name_ar', 'تطبيق المدينة');
            $siteLogo = \App\Models\AppSetting::get('site_logo');
            $siteFavicon = \App\Models\AppSetting::get('site_favicon');
            $siteContactPhone = \App\Models\AppSetting::get('contact_phone');
            $siteContactEmail = \App\Models\AppSetting::get('contact_email');
            $siteContactAddress = \App\Models\AppSetting::get('contact_address');
            $siteFacebookUrl = \App\Models\AppSetting::get('facebook_url');
            $siteTwitterUrl = \App\Models\AppSetting::get('twitter_url');
            $siteInstagramUrl = \App\Models\AppSetting::get('instagram_url');
            $siteYoutubeUrl = \App\Models\AppSetting::get('youtube_url');
            $siteMetaTitle = \App\Models\AppSetting::get('meta_title');
            $siteMetaTitleAr = \App\Models\AppSetting::get('meta_title_ar');
            $siteMetaDescription = \App\Models\AppSetting::get('meta_description');
            $siteMetaDescriptionAr = \App\Models\AppSetting::get('meta_description_ar');
            $siteMetaKeywords = \App\Models\AppSetting::get('meta_keywords');
            $siteMetaKeywordsAr = \App\Models\AppSetting::get('meta_keywords_ar');
            $siteOgImage = \App\Models\AppSetting::get('og_image');
            $siteGoogleAnalyticsId = \App\Models\AppSetting::get('google_analytics_id');
            $siteFacebookPixelId = \App\Models\AppSetting::get('facebook_pixel_id');
            
            if ($currentCity) {
                $view->with('currentCity', $currentCity);
                $view->with('citySettings', [
                    'name' => $currentCity->name ?? $siteName,
                    'name_ar' => $currentCity->name_ar ?? $currentCity->name ?? $siteNameAr,
                    'logo' => $currentCity->logo_url ?? ($siteLogo ? asset('storage/' . $siteLogo) : null),
                    'favicon' => $currentCity->favicon_url ?? ($siteFavicon ? asset('storage/' . $siteFavicon) : null),
                    'primary_color' => $currentCity->primary_color ?? '#3B82F6',
                    'secondary_color' => $currentCity->secondary_color ?? '#10B981',
                    'accent_color' => $currentCity->accent_color ?? '#F59E0B',
                    'contact_phone' => $currentCity->contact_phone ?? $siteContactPhone,
                    'contact_email' => $currentCity->contact_email ?? $siteContactEmail,
                    'contact_address' => $currentCity->contact_address ?? $siteContactAddress,
                    'contact_whatsapp' => $currentCity->contact_whatsapp,
                    'facebook_url' => $currentCity->facebook_url ?? $siteFacebookUrl,
                    'twitter_url' => $currentCity->twitter_url ?? $siteTwitterUrl,
                    'instagram_url' => $currentCity->instagram_url ?? $siteInstagramUrl,
                    'youtube_url' => $currentCity->youtube_url ?? $siteYoutubeUrl,
                    'meta_title' => $currentCity->meta_title ?? $siteMetaTitle,
                    'meta_title_ar' => $currentCity->meta_title_ar ?? $siteMetaTitleAr,
                    'meta_description' => $currentCity->meta_description ?? $siteMetaDescription,
                    'meta_description_ar' => $currentCity->meta_description_ar ?? $siteMetaDescriptionAr,
                    'meta_keywords' => $currentCity->meta_keywords ?? $siteMetaKeywords,
                    'meta_keywords_ar' => $currentCity->meta_keywords_ar ?? $siteMetaKeywordsAr,
                    'og_image' => $currentCity->og_image_url ?? ($siteOgImage ? asset('storage/' . $siteOgImage) : null),
                    'google_analytics_id' => $currentCity->google_analytics_id ?? $siteGoogleAnalyticsId,
                    'facebook_pixel_id' => $currentCity->facebook_pixel_id ?? $siteFacebookPixelId,
                ]);
            } else {
                // No city available, use site-wide settings only
                $view->with('currentCity', null);
                $view->with('citySettings', [
                    'name' => $siteName,
                    'name_ar' => $siteNameAr,
                    'logo' => $siteLogo ? asset('storage/' . $siteLogo) : null,
                    'favicon' => $siteFavicon ? asset('storage/' . $siteFavicon) : null,
                    'primary_color' => '#3B82F6',
                    'secondary_color' => '#10B981',
                    'accent_color' => '#F59E0B',
                    'contact_phone' => $siteContactPhone,
                    'contact_email' => $siteContactEmail,
                    'contact_address' => $siteContactAddress,
                    'contact_whatsapp' => null,
                    'facebook_url' => $siteFacebookUrl,
                    'twitter_url' => $siteTwitterUrl,
                    'instagram_url' => $siteInstagramUrl,
                    'youtube_url' => $siteYoutubeUrl,
                    'meta_title' => $siteMetaTitle,
                    'meta_title_ar' => $siteMetaTitleAr,
                    'meta_description' => $siteMetaDescription,
                    'meta_description_ar' => $siteMetaDescriptionAr,
                    'meta_keywords' => $siteMetaKeywords,
                    'meta_keywords_ar' => $siteMetaKeywordsAr,
                    'og_image' => $siteOgImage ? asset('storage/' . $siteOgImage) : null,
                    'google_analytics_id' => $siteGoogleAnalyticsId,
                    'facebook_pixel_id' => $siteFacebookPixelId,
                ]);
            }
        });
    }

    /**
     * Get the current city based on session, request, or default
     */
    protected function getCurrentCity()
    {
        // Try to get city from session
        $cityId = session('current_city_id');
        
        // If not in session, try to get from request
        if (!$cityId && request()->has('city')) {
            $citySlug = request()->get('city');
            $city = Cache::remember("city_by_slug_{$citySlug}", 3600, function () use ($citySlug) {
                return City::where('slug', $citySlug)->where('is_active', true)->first();
            });
            
            if ($city) {
                session(['current_city_id' => $city->id]);
                return $city;
            }
        }
        
        // If we have a city ID in session, load it
        if ($cityId) {
            return Cache::remember("city_{$cityId}", 3600, function () use ($cityId) {
                return City::find($cityId);
            });
        }
        
        // Default to first active city
        return Cache::remember('default_city', 3600, function () {
            return City::where('is_active', true)->first();
        });
    }
}
