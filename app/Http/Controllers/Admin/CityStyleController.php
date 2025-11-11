<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CityStyleController extends Controller
{
    public function index()
    {
        $cities = City::orderBy('name_ar')->paginate(20);
        return view('admin.city-styles.index', compact('cities'));
    }

    public function show(City $city)
    {
        return view('admin.city-styles.show', compact('city'));
    }

    public function edit(City $city)
    {
        return view('admin.city-styles.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'enable_custom_styling' => 'boolean',
            'primary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'background_style' => 'nullable|in:color,gradient,image',
            'background_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'background_gradient_start' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'background_gradient_end' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'font_family' => 'nullable|in:cairo,tajawal,amiri,noto_sans_arabic,default',
            'custom_css' => 'nullable|string|max:10000',
            'theme_config' => 'nullable|json',
        ]);

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            // Delete old hero image if exists
            if ($city->hero_image) {
                Storage::disk('public')->delete($city->hero_image);
            }
            
            $image = $request->file('hero_image');
            $imageName = 'city_hero_' . $city->id . '_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('city-themes', $imageName, 'public');
            $validated['hero_image'] = $imagePath;
        }

        // Process theme configuration
        $themeConfig = [
            'colors' => [
                'primary' => $validated['primary_color'] ?? '#3b82f6',
                'secondary' => $validated['secondary_color'] ?? '#64748b',
                'accent' => $validated['accent_color'] ?? '#f59e0b',
            ],
            'background' => [
                'style' => $validated['background_style'] ?? 'color',
                'color' => $validated['background_color'] ?? '#ffffff',
                'gradient' => [
                    'start' => $validated['background_gradient_start'] ?? '#f8fafc',
                    'end' => $validated['background_gradient_end'] ?? '#e2e8f0',
                ],
            ],
            'typography' => [
                'font_family' => $validated['font_family'] ?? 'cairo',
            ],
            'layout' => [
                'border_radius' => '8px',
                'shadow' => 'md',
            ]
        ];

        $validated['theme_config'] = json_encode($themeConfig);

        $city->update($validated);

        // Clear any cached CSS
        $this->clearCityThemeCache($city);

        return redirect()->route('admin.city-styles.index')
                        ->with('success', 'تم تحديث تصميم المدينة بنجاح');
    }

    public function generateCss(City $city)
    {
        $css = $city->getCityThemeCss();
        
        return response($css, 200, [
            'Content-Type' => 'text/css',
            'Cache-Control' => 'public, max-age=86400', // Cache for 24 hours
        ]);
    }

    public function previewTheme(Request $request, City $city)
    {
        $themeData = $request->validate([
            'primary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'background_style' => 'nullable|in:color,gradient,image',
            'background_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'background_gradient_start' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'background_gradient_end' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'font_family' => 'nullable|in:cairo,tajawal,amiri,noto_sans_arabic,default',
        ]);

        // Generate preview CSS based on form data
        $previewTheme = [
            'colors' => [
                'primary' => $themeData['primary_color'] ?? '#3b82f6',
                'secondary' => $themeData['secondary_color'] ?? '#64748b',
                'accent' => $themeData['accent_color'] ?? '#f59e0b',
            ],
            'background' => [
                'style' => $themeData['background_style'] ?? 'color',
                'color' => $themeData['background_color'] ?? '#ffffff',
                'gradient' => [
                    'start' => $themeData['background_gradient_start'] ?? '#f8fafc',
                    'end' => $themeData['background_gradient_end'] ?? '#e2e8f0',
                ],
            ],
            'typography' => [
                'font_family' => $themeData['font_family'] ?? 'cairo',
            ],
        ];

        $css = $this->generateThemeCss($previewTheme);
        
        return response()->json([
            'css' => $css,
            'preview_url' => route('city.landing', $city->slug)
        ]);
    }

    public function resetTheme(City $city)
    {
        $city->update([
            'enable_custom_styling' => false,
            'primary_color' => null,
            'secondary_color' => null,
            'accent_color' => null,
            'background_style' => null,
            'background_color' => null,
            'background_gradient_start' => null,
            'background_gradient_end' => null,
            'font_family' => null,
            'custom_css' => null,
            'theme_config' => null,
        ]);

        // Delete hero image if exists
        if ($city->hero_image) {
            Storage::disk('public')->delete($city->hero_image);
            $city->update(['hero_image' => null]);
        }

        $this->clearCityThemeCache($city);

        return redirect()->route('admin.city-styles.edit', $city)
                        ->with('success', 'تم إعادة تعيين تصميم المدينة للوضع الافتراضي');
    }

    protected function clearCityThemeCache(City $city)
    {
        // Clear any cached CSS or theme data
        cache()->forget("city_theme_css_{$city->id}");
        cache()->forget("city_theme_config_{$city->id}");
    }

    protected function generateThemeCss(array $theme): string
    {
        $primaryColor = $theme['colors']['primary'];
        $secondaryColor = $theme['colors']['secondary'];
        $accentColor = $theme['colors']['accent'];
        $fontFamily = $theme['typography']['font_family'];

        $backgroundCss = '';
        if ($theme['background']['style'] === 'gradient') {
            $backgroundCss = "background: linear-gradient(135deg, {$theme['background']['gradient']['start']}, {$theme['background']['gradient']['end']});";
        } else {
            $backgroundCss = "background-color: {$theme['background']['color']};";
        }

        $fontCss = '';
        switch ($fontFamily) {
            case 'cairo':
                $fontCss = "font-family: 'Cairo', sans-serif;";
                break;
            case 'tajawal':
                $fontCss = "font-family: 'Tajawal', sans-serif;";
                break;
            case 'amiri':
                $fontCss = "font-family: 'Amiri', serif;";
                break;
            case 'noto_sans_arabic':
                $fontCss = "font-family: 'Noto Sans Arabic', sans-serif;";
                break;
            default:
                $fontCss = "font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;";
        }

        return "
        .city-theme {
            {$backgroundCss}
            {$fontCss}
        }
        
        .city-theme .btn-primary {
            background-color: {$primaryColor};
            border-color: {$primaryColor};
        }
        
        .city-theme .btn-primary:hover {
            background-color: " . $this->darkenColor($primaryColor, 10) . ";
            border-color: " . $this->darkenColor($primaryColor, 10) . ";
        }
        
        .city-theme .text-primary {
            color: {$primaryColor} !important;
        }
        
        .city-theme .bg-primary {
            background-color: {$primaryColor} !important;
        }
        
        .city-theme .border-primary {
            border-color: {$primaryColor} !important;
        }
        
        .city-theme .text-secondary {
            color: {$secondaryColor} !important;
        }
        
        .city-theme .bg-secondary {
            background-color: {$secondaryColor} !important;
        }
        
        .city-theme .text-accent {
            color: {$accentColor} !important;
        }
        
        .city-theme .bg-accent {
            background-color: {$accentColor} !important;
        }
        
        .city-theme .city-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: {$primaryColor};
        }
        
        .city-theme .shop-card:hover {
            border-color: {$accentColor};
        }
        ";
    }

    protected function darkenColor(string $color, int $percent): string
    {
        $color = ltrim($color, '#');
        $rgb = str_split($color, 2);
        $darker = [];
        
        foreach ($rgb as $component) {
            $decimal = hexdec($component);
            $darker[] = str_pad(dechex(max(0, $decimal - ($decimal * $percent / 100))), 2, '0', STR_PAD_LEFT);
        }
        
        return '#' . implode('', $darker);
    }

    /**
     * Edit landing page theme configuration
     */
    public function editLandingPage(City $city)
    {
        return view('admin.city-styles.landing-page', compact('city'));
    }

    /**
     * Update landing page theme configuration
     */
    public function updateLandingPage(Request $request, City $city)
    {
        $validated = $request->validate([
            'primary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'banner_style' => 'nullable|in:carousel,grid,slider',
            'show_featured_section' => 'boolean',
            'show_latest_section' => 'boolean',
            'show_statistics' => 'boolean',
            'featured_shops_limit' => 'nullable|integer|min:3|max:20',
            'latest_shops_limit' => 'nullable|integer|min:5|max:30',
            'category_display_style' => 'nullable|in:grid,list,carousel',
        ]);

        $themeConfig = [
            'primary_color' => $validated['primary_color'] ?? '#FF5733',
            'secondary_color' => $validated['secondary_color'] ?? '#33FF57',
            'accent_color' => $validated['accent_color'] ?? '#FFC300',
            'banner_style' => $validated['banner_style'] ?? 'carousel',
            'show_featured_section' => $request->has('show_featured_section'),
            'show_latest_section' => $request->has('show_latest_section'),
            'show_statistics' => $request->has('show_statistics'),
            'featured_shops_limit' => $validated['featured_shops_limit'] ?? 10,
            'latest_shops_limit' => $validated['latest_shops_limit'] ?? 15,
            'category_display_style' => $validated['category_display_style'] ?? 'grid',
        ];

        $city->update(['theme_config' => $themeConfig]);

        return redirect()
            ->route('admin.city-styles.landing-page', $city)
            ->with('success', 'تم تحديث إعدادات الصفحة الرئيسية بنجاح!');
    }
}