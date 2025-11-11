<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'max_shops',
        'max_products_per_shop',
        'max_services_per_shop',
        'max_images_per_shop',
        'analytics_access',
        'priority_listing',
        'verified_badge',
        'custom_branding',
        'social_media_integration',
        'email_marketing',
        'advanced_seo',
        'customer_support',
        'features',
        'is_active',
        'is_popular',
        'sort_order'
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'analytics_access' => 'boolean',
        'priority_listing' => 'boolean',
        'verified_badge' => 'boolean',
        'custom_branding' => 'boolean',
        'social_media_integration' => 'boolean',
        'email_marketing' => 'boolean',
        'advanced_seo' => 'boolean',
        'customer_support' => 'boolean',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean'
    ];

    public function shopSubscriptions(): HasMany
    {
        return $this->hasMany(ShopSubscription::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->shopSubscriptions();
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(ShopSubscription::class)->where('status', 'active');
    }

    public function getFormattedMonthlyPriceAttribute(): string
    {
        return number_format($this->monthly_price, 2) . ' جنيه/شهر';
    }

    public function getFormattedYearlyPriceAttribute(): string
    {
        return number_format($this->yearly_price, 2) . ' جنيه/سنة';
    }

    public function getYearlySavingsAttribute(): float
    {
        return ($this->monthly_price * 12) - $this->yearly_price;
    }

    public function getYearlySavingsPercentageAttribute(): int
    {
        if ($this->monthly_price == 0) return 0;
        $yearlyTotal = $this->monthly_price * 12;
        return round((($yearlyTotal - $this->yearly_price) / $yearlyTotal) * 100);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('monthly_price');
    }
}