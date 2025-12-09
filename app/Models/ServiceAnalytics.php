<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_service_id',
        'date',
        'views',
        'contacts',
        'phone_clicks',
        'whatsapp_clicks',
        'unique_visitors',
        'referrer_sources',
        'visitor_locations',
    ];

    protected $casts = [
        'date' => 'date',
        'referrer_sources' => 'array',
        'visitor_locations' => 'array',
    ];

    /**
     * Get the user service that owns this analytics record
     */
    public function userService(): BelongsTo
    {
        return $this->belongsTo(UserService::class);
    }
}