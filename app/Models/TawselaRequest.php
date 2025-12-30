<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TawselaRequest extends Model
{
    protected $fillable = [
        'ride_id',
        'user_id',
        'pickup_latitude',
        'pickup_longitude',
        'pickup_address',
        'dropoff_latitude',
        'dropoff_longitude',
        'dropoff_address',
        'passengers_count',
        'offered_price',
        'message',
        'status',
    ];

    protected $casts = [
        'pickup_latitude' => 'decimal:8',
        'pickup_longitude' => 'decimal:8',
        'dropoff_latitude' => 'decimal:8',
        'dropoff_longitude' => 'decimal:8',
        'offered_price' => 'decimal:2',
        'passengers_count' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($request) {
            $request->ride->incrementRequests();
        });

        static::updated(function ($request) {
            // Notify ride owner if status changed
            if ($request->isDirty('status')) {
                // Send notification
            }
        });
    }

    /**
     * Relationships
     */
    public function ride(): BelongsTo
    {
        return $this->belongsTo(TawselaRide::class, 'ride_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TawselaMessage::class, 'request_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Methods
     */
    public function accept()
    {
        $this->update(['status' => 'accepted']);
    }

    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }
}
