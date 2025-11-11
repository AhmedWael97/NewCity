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
        'metric_type',
        'metric_value',
        'value',
        'date',
        'hour',
        'user_agent',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'metadata' => 'array',
    ];

    /**
     * Get the user service that owns this analytics record
     */
    public function userService(): BelongsTo
    {
        return $this->belongsTo(UserService::class);
    }
}