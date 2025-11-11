<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Company Contact Information
    |--------------------------------------------------------------------------
    |
    | Here you may configure all the contact information for your company.
    | This information will be used throughout the application and cached
    | for better performance.
    |
    */

    'company' => env('COMPANY_NAME', 'اكتشف المدن مصر'),
    'email' => env('CONTACT_EMAIL', 'info@discovercities.eg'),
    'phone' => env('CONTACT_PHONE', '+20 2 1234 5678'),
    'phone_display' => env('CONTACT_PHONE_DISPLAY', '+20 2 1234 5678'),
    'whatsapp' => env('WHATSAPP_NUMBER', '+20 10 1234 5678'),
    'country' => env('COMPANY_COUNTRY', 'مصر'),
    'city' => env('COMPANY_CITY', 'القاهرة'),
    'address' => env('COMPANY_ADDRESS', 'مصر الجديدة، القاهرة، جمهورية مصر العربية'),
    
    // Social Media Links
    'social' => [
        'facebook' => env('FACEBOOK_URL', ''),
        'twitter' => env('TWITTER_URL', ''),
        'instagram' => env('INSTAGRAM_URL', ''),
        'linkedin' => env('LINKEDIN_URL', ''),
        'youtube' => env('YOUTUBE_URL', ''),
    ],
    
    // Business Hours
    'business_hours' => [
        'weekdays' => env('BUSINESS_HOURS_WEEKDAYS', 'السبت - الخميس: 9:00 ص - 6:00 م'),
        'friday' => env('BUSINESS_HOURS_FRIDAY', 'الجمعة: 2:00 م - 6:00 م'),
        'timezone' => env('BUSINESS_TIMEZONE', 'Africa/Cairo'),
    ],
    
    // Support Information
    'support' => [
        'email' => env('SUPPORT_EMAIL', 'support@discovercities.eg'),
        'phone' => env('SUPPORT_PHONE', '+20 2 1234 5679'),
        'hours' => env('SUPPORT_HOURS', '24/7'),
    ],
];