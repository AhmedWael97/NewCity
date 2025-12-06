<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'state',
        'country',
        'latitude',
        'longitude',
        'description',
        'image',
        'is_active',
        // Contact Information
        'contact_phone',
        'contact_email',
        'contact_address',
        'contact_whatsapp',
        // SEO Settings
        'meta_title',
        'meta_title_ar',
        'meta_description',
        'meta_description_ar',
        'meta_keywords',
        'meta_keywords_ar',
        // Branding
        'logo',
        'favicon',
        'og_image',
        // Social Media
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'youtube_url',
        // Analytics
        'google_analytics_id',
        'facebook_pixel_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'elevation' => 'integer',
        'area' => 'decimal:2',
        'population' => 'integer',
        'founded_year' => 'integer',
        'key_features' => 'array',
        'coordinates' => 'array',
        'featured' => 'boolean',
        'is_active' => 'boolean',
        'featured_shops_count' => 'integer',
        // Styling casts
        'theme_config' => 'array',
        'enable_custom_styling' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the full URL for the city image
     */
    public function getImageAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        
        return url('storage/' . $value);
    }

    /**
     * Get the full URL for the hero image
     */
    public function getHeroImageAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        
        return url('storage/' . $value);
    }

    /**
     * Get the full URL for the city logo
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }
        
        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }
        
        return asset('storage/' . $this->logo);
    }

    /**
     * Get the full URL for the city favicon
     */
    public function getFaviconUrlAttribute()
    {
        if (!$this->favicon) {
            return null;
        }
        
        if (str_starts_with($this->favicon, 'http://') || str_starts_with($this->favicon, 'https://')) {
            return $this->favicon;
        }
        
        return asset('storage/' . $this->favicon);
    }

    /**
     * Get the full URL for the OG image
     */
    public function getOgImageUrlAttribute()
    {
        if (!$this->og_image) {
            return null;
        }
        
        if (str_starts_with($this->og_image, 'http://') || str_starts_with($this->og_image, 'https://')) {
            return $this->og_image;
        }
        
        return asset('storage/' . $this->og_image);
    }

    /**
     * Get all shops in this city
     */
    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    /**
     * Get all users in this city
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get active shops in this city
     */
    public function activeShops(): HasMany
    {
        return $this->shops()->where('is_active', true);
    }

    /**
     * Get categories that have shops in this city
     */
    public function categories()
    {
        return $this->hasManyThrough(
            Category::class,
            Shop::class,
            'city_id',      // Foreign key on shops table
            'id',           // Foreign key on categories table
            'id',           // Local key on cities table
            'category_id'   // Local key on shops table
        )->distinct();
    }

    /**
     * Get banners for this city
     */
    public function banners(): HasMany
    {
        return $this->hasMany(CityBanner::class);
    }

    /**
     * Get active banners for this city
     */
    public function activeBanners(): HasMany
    {
        return $this->banners()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('priority', 'asc');
    }

    /**
     * Scope to get only active cities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get featured cities
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope to get cities by governorate
     */
    public function scopeInGovernorate($query, $governorate)
    {
        return $query->where('governorate', $governorate);
    }

    /**
     * Scope to get new cities only
     */
    public function scopeNewCities($query)
    {
        return $query->where('development_type', 'new_city');
    }

    /**
     * Get formatted population
     */
    public function getFormattedPopulationAttribute()
    {
        if (!$this->population) return 'غير محدد';
        
        if ($this->population >= 1000000) {
            return number_format($this->population / 1000000, 1) . ' مليون';
        } elseif ($this->population >= 1000) {
            return number_format($this->population / 1000, 0) . ' ألف';
        }
        
        return number_format($this->population);
    }

    /**
     * Get display name (just return the name since no Arabic field exists)
     */
    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get display description (just return the description since no Arabic field exists)
     */
    public function getDisplayDescriptionAttribute()
    {
        return $this->description;
    }

    /**
     * Check if this is a new city
     */
    public function getIsNewCityAttribute()
    {
        return $this->development_type === 'new_city';
    }

    /**
     * Get status in Arabic
     */
    public function getStatusArabicAttribute()
    {
        return match($this->status) {
            'planned' => 'مخططة',
            'under_development' => 'تحت التطوير',
            'established' => 'مكتملة',
            'expanding' => 'قيد التوسع',
            default => 'غير محدد'
        };
    }

    /**
     * Get advertisements for this city
     */
    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class);
    }

    /**
     * Get city-specific styling
     */
    public function getThemeStyles(): array
    {
        if (!$this->enable_custom_styling) {
            return $this->getDefaultTheme();
        }

        return [
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'accent_color' => $this->accent_color,
            'background_style' => $this->background_style,
            'font_family' => $this->font_family,
            'hero_image' => $this->hero_image,
            'custom_css' => $this->custom_css,
            'theme_config' => $this->theme_config ?? []
        ];
    }

    /**
     * Get default theme for city
     */
    public function getDefaultTheme(): array
    {
        return [
            'primary_color' => '#667eea',
            'secondary_color' => '#764ba2',
            'accent_color' => '#f093fb',
            'background_style' => 'gradient',
            'font_family' => 'Cairo',
            'hero_image' => null,
            'custom_css' => null,
            'theme_config' => []
        ];
    }

    /**
     * Generate CSS variables for city theme
     */
    public function getCityThemeCss(): string
    {
        $styles = $this->getThemeStyles();
        
        $css = ":root {\n";
        $css .= "  --city-primary: {$styles['primary_color']};\n";
        $css .= "  --city-secondary: {$styles['secondary_color']};\n";
        $css .= "  --city-accent: {$styles['accent_color']};\n";
        $css .= "  --city-font: '{$styles['font_family']}';\n";
        $css .= "}\n\n";

        // Background style
        if ($styles['background_style'] === 'gradient') {
            $css .= ".city-hero { background: linear-gradient(135deg, {$styles['primary_color']}, {$styles['secondary_color']}) !important; }\n";
        } elseif ($styles['background_style'] === 'solid') {
            $css .= ".city-hero { background: {$styles['primary_color']} !important; }\n";
        } elseif ($styles['background_style'] === 'image' && $styles['hero_image']) {
            $css .= ".city-hero { background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{$styles['hero_image']}') center/cover !important; }\n";
        }

        // Font family
        $css .= "body { font-family: {$styles['font_family']}, 'Cairo', sans-serif !important; }\n";

        // Custom CSS
        if ($styles['custom_css']) {
            $css .= "\n/* Custom City CSS */\n";
            $css .= $styles['custom_css'];
        }

        return $css;
    }

    /**
     * Get theme configuration value with dot notation support
     *
     * @param string $key Key in dot notation (e.g., 'colors.primary')
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public function getThemeConfig(string $key, $default = null)
    {
        // Decode theme_config if it's JSON
        $config = $this->theme_config;
        
        if (is_string($config)) {
            $config = json_decode($config, true) ?? [];
        }
        
        if (!is_array($config)) {
            $config = [];
        }

        // Support dot notation for nested values
        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value ?? $default;
    }
}
