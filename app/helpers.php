<?php

use App\Models\City;
use Illuminate\Support\Facades\Cache;

if (!function_exists('current_city')) {
    /**
     * Get the current active city
     */
    function current_city()
    {
        $cityId = session('current_city_id');
        
        if ($cityId) {
            return Cache::remember("city_{$cityId}", 3600, function () use ($cityId) {
                return City::find($cityId);
            });
        }
        
        return Cache::remember('default_city', 3600, function () {
            return City::where('is_active', true)->first();
        });
    }
}

if (!function_exists('city_setting')) {
    /**
     * Get a specific city setting
     */
    function city_setting($key, $default = null)
    {
        $city = current_city();
        
        if (!$city) {
            return $default;
        }
        
        return $city->$key ?? $default;
    }
}

if (!function_exists('city_logo')) {
    /**
     * Get the current city logo URL
     */
    function city_logo()
    {
        $city = current_city();
        return $city ? $city->logo_url : asset('images/default-logo.png');
    }
}

if (!function_exists('city_favicon')) {
    /**
     * Get the current city favicon URL
     */
    function city_favicon()
    {
        $city = current_city();
        return $city ? $city->favicon_url : asset('images/default-favicon.ico');
    }
}

if (!function_exists('city_meta_title')) {
    /**
     * Get the city meta title
     */
    function city_meta_title($lang = 'ar')
    {
        $city = current_city();
        
        if (!$city) {
            return config('app.name');
        }
        
        if ($lang === 'ar') {
            return $city->meta_title_ar ?: $city->name_ar ?: $city->name;
        }
        
        return $city->meta_title ?: $city->name;
    }
}

if (!function_exists('city_meta_description')) {
    /**
     * Get the city meta description
     */
    function city_meta_description($lang = 'ar')
    {
        $city = current_city();
        
        if (!$city) {
            return '';
        }
        
        if ($lang === 'ar') {
            return $city->meta_description_ar ?: $city->description;
        }
        
        return $city->meta_description ?: $city->description;
    }
}

if (!function_exists('city_color')) {
    /**
     * Get city color (primary, secondary, accent)
     */
    function city_color($type = 'primary')
    {
        $city = current_city();
        
        if (!$city) {
            $defaults = [
                'primary' => '#3B82F6',
                'secondary' => '#10B981',
                'accent' => '#F59E0B',
            ];
            return $defaults[$type] ?? '#3B82F6';
        }
        
        $field = $type . '_color';
        return $city->$field ?? '#3B82F6';
    }
}

if (!function_exists('city_contact')) {
    /**
     * Get city contact info (phone, email, address, whatsapp)
     */
    function city_contact($type = 'phone')
    {
        $city = current_city();
        
        if (!$city) {
            return null;
        }
        
        $field = 'contact_' . $type;
        return $city->$field;
    }
}
