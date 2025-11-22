<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
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
        'sku',
        'stock_quantity',
        'is_available',
        'is_featured',
        'specifications',
        'unit',
        'weight',
        'brand',
        'sort_order',
    ];

    protected $casts = [
        'specifications' => 'array',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name . '-' . Str::random(6));
            }
        });
    }

    /**
     * Get the images as an array with full URLs
     */
    public function getImagesAttribute($value)
    {
        $images = json_decode($value, true) ?: [];
        
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
     * التحقق من توفر المنتج
     */
    public function getInStockAttribute()
    {
        return $this->is_available && $this->stock_quantity > 0;
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

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
}
