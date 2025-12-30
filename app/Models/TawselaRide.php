<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TawselaRide extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'city_id',
        'car_model',
        'car_year',
        'car_color',
        'available_seats',
        'start_latitude',
        'start_longitude',
        'start_address',
        'destination_latitude',
        'destination_longitude',
        'destination_address',
        'stop_points',
        'price',
        'price_type',
        'price_unit',
        'departure_time',
        'notes',
        'status',
        'views_count',
        'requests_count',
    ];

    protected $casts = [
        'start_latitude' => 'decimal:8',
        'start_longitude' => 'decimal:8',
        'destination_latitude' => 'decimal:8',
        'destination_longitude' => 'decimal:8',
        'price' => 'decimal:2',
        'car_year' => 'integer',
        'available_seats' => 'integer',
        'views_count' => 'integer',
        'requests_count' => 'integer',
        'stop_points' => 'array',
        'departure_time' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($ride) {
            // Any post-creation logic
        });
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(TawselaRequest::class, 'ride_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TawselaMessage::class, 'ride_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('departure_time', '>', now());
    }

    public function scopeInCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Methods
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementRequests()
    {
        $this->increment('requests_count');
    }

    public function hasAvailableSeats()
    {
        $acceptedRequests = $this->requests()
            ->where('status', 'accepted')
            ->sum('passengers_count');
        
        return $this->available_seats > $acceptedRequests;
    }

    public function getRemainingSeats()
    {
        $acceptedRequests = $this->requests()
            ->where('status', 'accepted')
            ->sum('passengers_count');
        
        return max(0, $this->available_seats - $acceptedRequests);
    }

    /**
     * Calculate distance between two points (Haversine formula)
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return $distance;
    }

    /**
     * Search for rides near a location
     */
    public static function searchNearby($latitude, $longitude, $maxDistance = 10)
    {
        return static::selectRaw("
            *,
            (
                6371 * acos(
                    cos(radians(?)) * cos(radians(start_latitude)) *
                    cos(radians(start_longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(start_latitude))
                )
            ) AS distance
        ", [$latitude, $longitude, $latitude])
        ->having('distance', '<=', $maxDistance)
        ->orderBy('distance');
    }
}
