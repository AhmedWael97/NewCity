<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'service_category_id',
        'city_id',
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
        'images' => 'array',
        'vehicle_info' => 'array',
        'certifications' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'featured_until' => 'datetime',
        'subscription_expires_at' => 'datetime',
    ];

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
     * Get the first image
     */
    public function getFirstImageAttribute()
    {
        if (is_array($this->images) && count($this->images) > 0) {
            return asset('storage/' . $this->images[0]);
        }
        return asset('images/default-service.jpg');
    }

    /**
     * Get images array safely
     */
    public function getImagesArrayAttribute()
    {
        if (is_string($this->images)) {
            return json_decode($this->images, true) ?? [];
        }
        return is_array($this->images) ? $this->images : [];
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
            ->where('metric_type', 'view')
            ->where('date', '>=', now()->startOfMonth())
            ->sum('value');
    }

    /**
     * Get monthly contacts
     */
    public function getMonthlyContactsAttribute()
    {
        return $this->analytics()
            ->where('metric_type', 'contact')
            ->where('date', '>=', now()->startOfMonth())
            ->sum('value');
    }

    /**
     * Scope for active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('status', 'approved');
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
}
