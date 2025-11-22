<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserService extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_category_id',
        'city_id',
        'slug',
        'hashed_id',
        'title',
        'description',
        'pricing_type',
        'base_price',
        'hourly_rate',
        'distance_rate',
        'minimum_charge',
        'availability_schedule',
        'contact_phone',
        'contact_whatsapp',
        'service_area',
        'requirements',
        'images',
        'vehicle_info',
        'experience_years',
        'certifications',
        'is_active',
        'is_verified',
        'featured_until',
        'subscription_plan_id',
        'subscription_expires_at',
        'status'
    ];

    protected $casts = [
        'availability_schedule' => 'array',
        'service_area' => 'array',
        'requirements' => 'array',
        'vehicle_info' => 'array',
        'certifications' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'featured_until' => 'datetime',
        'subscription_expires_at' => 'datetime',
    ];

    protected $appends = ['hashed_id'];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $slug = \Illuminate\Support\Str::slug($service->title);
                $count = 1;
                $originalSlug = $slug;
                
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                
                $service->slug = $slug;
            }
        });
    }

    /**
     * Get the user that owns the service
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service category
     */
    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /**
     * Get the service category (alias for compatibility)
     */
    public function category()
    {
        return $this->serviceCategory();
    }

    /**
     * Get the city where service is offered
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the subscription plan
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Get all bookings for this service
     */
    public function bookings()
    {
        return $this->hasMany(ServiceBooking::class);
    }

    /**
     * Get all reviews for this service
     */
    public function reviews()
    {
        return $this->hasMany(ServiceReview::class);
    }

    /**
     * Get all analytics records
     */
    public function analytics()
    {
        return $this->hasMany(ServiceAnalytics::class);
    }

    /**
     * Get the images attribute with full URLs
     */
    public function getImagesAttribute($value)
    {
        $images = json_decode($value, true) ?? [];
        
        if (empty($images)) {
            return [];
        }
        
        // Convert to full URLs
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
     * Get the first image
     */
    public function getFirstImageAttribute()
    {
        $images = $this->images;
        if (!empty($images)) {
            return $images[0];
        }
        return url('images/default-service.jpg');
    }

    /**
     * Get images array safely with full URLs (alias)
     */
    public function getImagesArrayAttribute()
    {
        return $this->images;
    }

    /**
     * Check if service is featured
     */
    public function getIsFeaturedAttribute()
    {
        return $this->featured_until && $this->featured_until->isFuture();
    }

    /**
     * Check if subscription is active
     */
    public function getIsSubscriptionActiveAttribute()
    {
        return $this->subscription_expires_at && $this->subscription_expires_at->isFuture();
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get total reviews count
     */
    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Get total bookings count
     */
    public function getTotalBookingsAttribute()
    {
        return $this->bookings()->count();
    }

    /**
     * Get monthly views
     */
    public function getMonthlyViewsAttribute()
    {
        return $this->analytics()
            ->where('date', '>=', now()->startOfMonth())
            ->sum('views');
    }

    /**
     * Get monthly contacts
     */
    public function getMonthlyContactsAttribute()
    {
        return $this->analytics()
            ->where('date', '>=', now()->startOfMonth())
            ->sum('contacts');
    }

    /**
     * Scope for active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured services
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured_until', '>', now());
    }

    /**
     * Scope for services in a specific city
     */
    public function scopeInCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope for services by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('service_category_id', $categoryId);
    }

    /**
     * Scope for services with active subscription
     */
    public function scopeWithActiveSubscription($query)
    {
        return $query->where('subscription_expires_at', '>', now());
    }

    /**
     * Get hashed ID for URL
     */
    public function getHashedIdAttribute()
    {
        return base64_encode($this->id * 7919 + 1234); // Simple obfuscation
    }

    /**
     * Decode hashed ID to get actual ID
     */
    public static function decodeHashedId($hashedId)
    {
        try {
            $decoded = base64_decode($hashedId);
            if ($decoded === false) {
                return null;
            }
            return ($decoded - 1234) / 7919;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get route key name for model binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
