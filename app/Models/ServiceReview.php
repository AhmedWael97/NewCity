<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_service_id',
        'reviewer_id',
        'rating',
        'comment',
        'images',
        'is_verified',
        'is_approved',
        'service_date',
    ];

    protected $casts = [
        'images' => 'array',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'service_date' => 'datetime',
    ];

    /**
     * Get the service this review belongs to
     */
    public function userService()
    {
        return $this->belongsTo(UserService::class);
    }

    /**
     * Get the reviewer (user who wrote the review)
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Scope for approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for verified reviews
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
