<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminEmailPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_suggestion',
        'city_suggestion',
        'shop_rate',
        'service_rate',
        'new_service',
        'new_marketplace',
        'new_user',
    ];

    protected $casts = [
        'shop_suggestion' => 'boolean',
        'city_suggestion' => 'boolean',
        'shop_rate' => 'boolean',
        'service_rate' => 'boolean',
        'new_service' => 'boolean',
        'new_marketplace' => 'boolean',
        'new_user' => 'boolean',
    ];

    /**
     * Get the user that owns the preferences
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user should receive notification for event type
     */
    public function shouldReceive(string $eventType): bool
    {
        return $this->{$eventType} ?? false;
    }
}
