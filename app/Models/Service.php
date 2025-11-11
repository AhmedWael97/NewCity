<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Service extends Model
{
    protected $fillable = [
        'shop_id',
        'name',
        'slug',
        'description',
        'price',
        'original_price',
        'discount_percentage',
        'images',
        'duration_minutes',
        'duration_text',
        'is_available',
        'is_featured',
        'requires_appointment',
        'requirements',
        'benefits',
        'category',
        'sort_order',
    ];

    protected $casts = [
        'images' => 'array',
        'requirements' => 'array',
        'benefits' => 'array',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'requires_appointment' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name . '-' . Str::random(6));
            }
        });
    }

    /**
     * العلاقة مع المتجر
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * الحصول على السعر النهائي بعد التخفيض
     */
    public function getFinalPriceAttribute()
    {
        if ($this->discount_percentage > 0 && $this->original_price) {
            return $this->original_price - ($this->original_price * $this->discount_percentage / 100);
        }
        
        return $this->price;
    }

    /**
     * التحقق من وجود تخفيض
     */
    public function getHasDiscountAttribute()
    {
        return $this->discount_percentage > 0 && $this->original_price && $this->original_price > $this->price;
    }

    /**
     * تنسيق مدة الخدمة
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->duration_text) {
            return $this->duration_text;
        }

        if ($this->duration_minutes) {
            if ($this->duration_minutes < 60) {
                return $this->duration_minutes . ' دقيقة';
            } else {
                $hours = floor($this->duration_minutes / 60);
                $minutes = $this->duration_minutes % 60;
                
                if ($minutes > 0) {
                    return $hours . ' ساعة و ' . $minutes . ' دقيقة';
                }
                
                return $hours . ' ساعة';
            }
        }

        return null;
    }

    /**
     * Scopes
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeRequiresAppointment($query)
    {
        return $query->where('requires_appointment', true);
    }
}
