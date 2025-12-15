<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'rating',
        'comment',
        'is_verified',
        'helpful_votes',
        'status'
    ];

    protected $casts = [
        'helpful_votes' => 'array',
        'is_verified' => 'boolean',
        'rating' => 'integer'
    ];

    /**
     * Get the user that owns the rating.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shop that owns the rating.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the rating as stars (for display).
     */
    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Check if the rating is helpful to a user.
     */
    public function isHelpfulTo(int $userId): bool
    {
        return in_array($userId, $this->helpful_votes ?? []);
    }

    /**
     * Get the count of helpful votes.
     */
    public function getHelpfulCountAttribute(): int
    {
        return count($this->helpful_votes ?? []);
    }

    /**
     * Scope to get ratings for a specific shop.
     */
    public function scopeForShop($query, int $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    /**
     * Scope to get ratings by a specific user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get verified ratings only.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get ratings with comments.
     */
    public function scopeWithComments($query)
    {
        return $query->whereNotNull('comment')->where('comment', '!=', '');
    }
}
