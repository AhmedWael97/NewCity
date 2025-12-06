<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display general settings form
     */
    public function index()
    {
        $settings = [
            'site_name' => AppSetting::get('site_name', 'City App'),
            'site_name_ar' => AppSetting::get('site_name_ar', 'تطبيق المدينة'),
            'site_tagline' => AppSetting::get('site_tagline', 'Your City Directory'),
            'site_tagline_ar' => AppSetting::get('site_tagline_ar', 'دليل مدينتك'),
            'site_description' => AppSetting::get('site_description', ''),
            'site_description_ar' => AppSetting::get('site_description_ar', ''),
            'site_logo' => AppSetting::get('site_logo'),
            'site_favicon' => AppSetting::get('site_favicon'),
            'contact_email' => AppSetting::get('contact_email', ''),
            'contact_phone' => AppSetting::get('contact_phone', ''),
            'contact_address' => AppSetting::get('contact_address', ''),
            'facebook_url' => AppSetting::get('facebook_url', ''),
            'twitter_url' => AppSetting::get('twitter_url', ''),
            'instagram_url' => AppSetting::get('instagram_url', ''),
            'youtube_url' => AppSetting::get('youtube_url', ''),
        ];

        return view('admin.site-settings.index', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_name_ar' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_tagline_ar' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'site_description_ar' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'site_favicon' => 'nullable|image|mimes:ico,png|max:1024',
        ]);

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            // Delete old logo
            $oldLogo = AppSetting::get('site_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            
            $logoPath = $request->file('site_logo')->store('settings', 'public');
            AppSetting::set('site_logo', $logoPath);
        }

        // Handle favicon upload
        if ($request->hasFile('site_favicon')) {
            // Delete old favicon
            $oldFavicon = AppSetting::get('site_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            
            $faviconPath = $request->file('site_favicon')->store('settings', 'public');
            AppSetting::set('site_favicon', $faviconPath);
        }

        // Update text settings
        $textSettings = [
            'site_name',
            'site_name_ar',
            'site_tagline',
            'site_tagline_ar',
            'site_description',
            'site_description_ar',
            'contact_email',
            'contact_phone',
            'contact_address',
            'facebook_url',
            'twitter_url',
            'instagram_url',
            'youtube_url',
        ];

        foreach ($textSettings as $key) {
            if ($request->has($key)) {
                AppSetting::set($key, $request->input($key));
            }
        }

        return redirect()
            ->route('admin.site-settings.index')
            ->with('success', 'تم تحديث الإعدادات بنجاح');
    }

    /**
     * Display SEO settings form
     */
    public function seo()
    {
        $settings = [
            'meta_title' => AppSetting::get('meta_title', ''),
            'meta_title_ar' => AppSetting::get('meta_title_ar', ''),
            'meta_description' => AppSetting::get('meta_description', ''),
            'meta_description_ar' => AppSetting::get('meta_description_ar', ''),
            'meta_keywords' => AppSetting::get('meta_keywords', ''),
            'meta_keywords_ar' => AppSetting::get('meta_keywords_ar', ''),
            'og_image' => AppSetting::get('og_image'),
            'google_analytics_id' => AppSetting::get('google_analytics_id', ''),
            'google_site_verification' => AppSetting::get('google_site_verification', ''),
            'facebook_pixel_id' => AppSetting::get('facebook_pixel_id', ''),
        ];

        return view('admin.site-settings.seo', compact('settings'));
    }

    /**
     * Update SEO settings
     */
    public function updateSeo(Request $request)
    {
        $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_title_ar' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_description_ar' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
            'meta_keywords_ar' => 'nullable|string',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'google_analytics_id' => 'nullable|string|max:50',
            'google_site_verification' => 'nullable|string',
            'facebook_pixel_id' => 'nullable|string|max:50',
        ]);

        // Handle OG image upload
        if ($request->hasFile('og_image')) {
            // Delete old image
            $oldImage = AppSetting::get('og_image');
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            
            $imagePath = $request->file('og_image')->store('settings', 'public');
            AppSetting::set('og_image', $imagePath);
        }

        // Update SEO settings
        $seoSettings = [
            'meta_title',
            'meta_title_ar',
            'meta_description',
            'meta_description_ar',
            'meta_keywords',
            'meta_keywords_ar',
            'google_analytics_id',
            'google_site_verification',
            'facebook_pixel_id',
        ];

        foreach ($seoSettings as $key) {
            if ($request->has($key)) {
                AppSetting::set($key, $request->input($key));
            }
        }

        return redirect()
            ->route('admin.site-settings.seo')
            ->with('success', 'تم تحديث إعدادات SEO بنجاح');
    }
}
