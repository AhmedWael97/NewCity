<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'user_id',
        'rating',
        'message',
        'email',
        'page_url',
        'ip_address',
        'user_agent',
        'submitted_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the user who submitted feedback
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: By rating
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope: Positive feedback (4-5 stars)
     */
    public function scopePositive($query)
    {
        return $query->whereIn('rating', [4, 5]);
    }

    /**
     * Scope: Negative feedback (1-2 stars)
     */
    public function scopeNegative($query)
    {
        return $query->whereIn('rating', [1, 2]);
    }
}
