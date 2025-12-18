<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopSuggestion extends Model
{
    protected $fillable = [
        'user_id',
        'city_id',
        'category_id',
        'shop_name',
        'description',
        'phone',
        'whatsapp',
        'email',
        'address',
        'google_maps_url',
        'latitude',
        'longitude',
        'website',
        'social_media',
        'opening_hours',
        'suggested_by_name',
        'suggested_by_phone',
        'suggested_by_email',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
        'location',
        
    ];

    protected $casts = [
        'social_media' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
