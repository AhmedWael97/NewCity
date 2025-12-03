<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'ip_address',
        'user_agent',
        'message',
        'email',
        'name',
        'is_verified',
        'verified_at',
        'country',
        'city',
        'browser',
        'device',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Check if session is verified
     */
    public static function isSessionVerified($sessionId): bool
    {
        return self::where('session_id', $sessionId)
            ->where('is_verified', true)
            ->exists();
    }

    /**
     * Get statistics
     */
    public static function getStats(): array
    {
        return [
            'total_verifications' => self::count(),
            'today' => self::whereDate('created_at', today())->count(),
            'this_week' => self::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => self::whereMonth('created_at', now()->month)->count(),
            'unique_ips' => self::distinct('ip_address')->count('ip_address'),
            'with_email' => self::whereNotNull('email')->count(),
            'recent' => self::latest()->take(10)->get(),
        ];
    }
}
