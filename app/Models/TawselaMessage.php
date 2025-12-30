<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TawselaMessage extends Model
{
    protected $fillable = [
        'ride_id',
        'request_id',
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($message) {
            // Send notification to receiver
        });
    }

    /**
     * Relationships
     */
    public function ride(): BelongsTo
    {
        return $this->belongsTo(TawselaRide::class, 'ride_id');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(TawselaRequest::class, 'request_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Scopes
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('sender_id', $userId)
              ->orWhere('receiver_id', $userId);
        });
    }

    /**
     * Methods
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}
