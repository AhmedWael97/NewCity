<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'city_id',
        'category_id',
        'name',
        'slug',
        'google_place_id',
        'google_types',
        'description',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'images',
        'opening_hours',
        'rating',
        'review_count',
        'is_featured',
        'featured_priority',
        'featured_until',
        'is_verified',
        'is_active',
        'verified_at',
        'verification_notes',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'images' => 'array',
        'google_types' => 'array',
        'opening_hours' => 'array',
        'rating' => 'decimal:2',
        'review_count' => 'integer',
        'is_featured' => 'boolean',
        'featured_priority' => 'integer',
        'featured_until' => 'datetime',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shop) {
            if (empty($shop->slug)) {
                $shop->slug = self::generateUniqueSlug($shop->name);
            }
        });

        static::updating(function ($shop) {
            if ($shop->isDirty('name') && empty($shop->slug)) {
                $shop->slug = self::generateUniqueSlug($shop->name);
            }
        });
    }

    /**
     * Generate a unique slug
     */
    private static function generateUniqueSlug($name)
    {
        $slug = \Illuminate\Support\Str::slug($name);
        
        // If slug is empty (Arabic text), use transliteration or ID
        if (empty($slug)) {
            $slug = 'shop-' . uniqid();
        }
        
        $originalSlug = $slug;
        $count = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Get the owner of this shop
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the owner of this shop (alias for user relationship)
     */
    public function owner(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Get the city this shop belongs to
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get users who favorited this shop
     */
    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'shop_user_favorites')->withTimestamps();
    }

    /**
     * Get the images attribute with full URLs
     */
    public function getImagesAttribute($value)
    {
        $images = json_decode($value, true) ?: [];
        
        if (empty($images)) {
            return [];
        }
        
        return array_map(function($image) {
            if (!$image) {
                return null;
            }
            if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
                return $image;
            }
            return url('storage/' . $image);
        }, $images);
    }

    /**
     * Get the images as an array (alias for compatibility)
     */
    public function getImagesArrayAttribute()
    {
        return $this->images;
    }

    /**
     * Get the opening hours as an array (handles both string and array formats)
     */
    public function getOpeningHoursArrayAttribute()
    {
        $hours = $this->opening_hours;
        
        if (is_string($hours)) {
            return json_decode($hours, true) ?: [];
        }
        
        return is_array($hours) ? $hours : [];
    }

    /**
     * Get the first image URL or null
     */
    public function getFirstImageAttribute()
    {
        $images = $this->images_array;
        return !empty($images) ? $images[0] : null;
    }

    /**
     * Get total views count from analytics
     */
    public function getTotalViewsAttribute()
    {
        return $this->analytics()
            ->where('event_type', 'shop_view')
            ->count();
    }

    /**
     * Get total ratings for display
     */
    public function getTotalRatingsAttribute()
    {
        return $this->ratings()->where('status', 'active')->count();
    }

    /**
     * Get average rating for display
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->where('status', 'active')->avg('rating') ?? 0;
    }

    /**
     * Get the category this shop belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the shop subscriptions
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(ShopSubscription::class);
    }

    /**
     * Get the active subscription for this shop
     */
    public function activeSubscription()
    {
        return $this->hasOne(ShopSubscription::class)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest();
    }

    /**
     * Check if shop has an active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Get the products for this shop
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the services for this shop
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get active products for this shop
     */
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('is_available', true)->orderBy('sort_order');
    }

    /**
     * Get active services for this shop
     */
    public function activeServices(): HasMany
    {
        return $this->hasMany(Service::class)->where('is_available', true)->orderBy('sort_order');
    }

    /**
     * Get featured products for this shop
     */
    public function featuredProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('is_featured', true)->where('is_available', true);
    }

    /**
     * Get featured services for this shop
     */
    public function featuredServices(): HasMany
    {
        return $this->hasMany(Service::class)->where('is_featured', true)->where('is_available', true);
    }

    /**
     * Get all ratings for this shop
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get all analytics for this shop
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(ShopAnalytics::class);
    }

    /**
     * Get average rating for this shop (only active ratings)
     */
    public function averageRating(): float
    {
        return $this->ratings()->where('status', 'active')->avg('rating') ?? 0;
    }

    /**
     * Get total rating count for this shop (only active ratings)
     */
    public function totalRatings(): int
    {
        return $this->ratings()->where('status', 'active')->count();
    }

    /**
     * Get ratings with comments
     */
    public function ratingsWithComments(): HasMany
    {
        return $this->ratings()->whereNotNull('comment')->where('comment', '!=', '');
    }

    /**
     * Get verified ratings only
     */
    public function verifiedRatings(): HasMany
    {
        return $this->ratings()->where('is_verified', true);
    }

    /**
     * Get rating distribution (count for each star rating)
     */
    public function getRatingDistribution(): array
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->ratings()->where('rating', $i)->count();
        }
        return $distribution;
    }

    /**
     * Update shop rating based on all ratings
     */
    public function updateRating(): void
    {
        $averageRating = $this->averageRating();
        $reviewCount = $this->totalRatings();
        
        $this->update([
            'rating' => round($averageRating, 2),
            'review_count' => $reviewCount
        ]);
    }

    /**
     * Scope to get only active shops
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only verified and active shops
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true)
                    ->where('is_active', true)
                    ->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope to get only featured shops
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get shops within radius
     */
    public function scopeWithinRadius($query, $latitude, $longitude, $radius = 10)
    {
        return $query->selectRaw("
            *,
            (6371 * acos(cos(radians(?)) * cos(radians(latitude))
            * cos(radians(longitude) - radians(?)) + sin(radians(?))
            * sin(radians(latitude)))) AS distance
        ", [$latitude, $longitude, $latitude])
        ->having('distance', '<', $radius)
        ->orderBy('distance');
    }

    /**
     * Check if shop is currently open
     */
    public function getIsOpenNowAttribute()
    {
        if (!$this->opening_hours) {
            return true; // Default to open if no hours specified
        }

        $currentDay = strtolower(now()->format('l')); // monday, tuesday, etc.
        $currentTime = now()->format('H:i');
        
        // Convert day names to Arabic or match your opening_hours format
        $dayMap = [
            'monday' => 'monday',
            'tuesday' => 'tuesday', 
            'wednesday' => 'wednesday',
            'thursday' => 'thursday',
            'friday' => 'friday',
            'saturday' => 'saturday',
            'sunday' => 'sunday'
        ];

        if (isset($this->opening_hours[$currentDay])) {
            $hours = $this->opening_hours[$currentDay];
            if (isset($hours['open']) && isset($hours['close'])) {
                return $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
            }
        }

        return true; // Default to open
    }

    /**
     * Check if shop is pending verification
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if shop is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if shop is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if shop is suspended
     */
    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Scope to get only pending shops
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get featured shops (active featured with valid date)
     */
    public function scopeActiveFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('featured_until')
                  ->orWhere('featured_until', '>=', now());
            })
            ->orderBy('featured_priority', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get recently added shops
     */
    public function scopeLatest($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
            ->where('is_active', true)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope to filter by city
     */
    public function scopeForCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Check if shop is currently featured
     */
    public function isFeatured()
    {
        if (!$this->is_featured) {
            return false;
        }

        if ($this->featured_until) {
            return now()->lte($this->featured_until);
        }

        return true;
    }
}
