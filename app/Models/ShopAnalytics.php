<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'user_id',
        'event_type',
        'user_ip',
        'user_agent',
        'referrer',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime'
    ];

    public $timestamps = false; // Only using created_at

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function track(int $shopId, string $eventType, ?int $userId = null, array $metadata = []): void
    {
        $analytics = new self([
            'shop_id' => $shopId,
            'user_id' => $userId,
            'event_type' => $eventType,
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer'),
            'metadata' => $metadata
        ]);
        
        $analytics->created_at = now();
        $analytics->save();
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeLastDays($query, int $days)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($analytics) {
            if (!$analytics->created_at) {
                $analytics->created_at = now();
            }
        });
    }
}