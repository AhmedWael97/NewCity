<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'title',
        'description',
        'image',
        'link_type',
        'link_url',
        'start_date',
        'end_date',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Get the full URL for the banner image
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
     * Get the city this banner belongs to
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Scope to get active banners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Scope to get banners for a specific city
     */
    public function scopeForCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Get active banners for a city ordered by priority
     */
    public static function getActiveBannersForCity($cityId)
    {
        return self::active()
            ->forCity($cityId)
            ->orderBy('priority', 'asc')
            ->get();
    }

    /**
     * Check if banner is currently active
     */
    public function isActive()
    {
        return $this->is_active &&
            now()->between($this->start_date, $this->end_date);
    }
}
