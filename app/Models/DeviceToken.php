<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_token',
        'device_type',
        'device_name',
        'app_version',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the device token
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get notification logs for this device
     */
    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    /**
     * Register or update a device token
     */
    public static function registerToken($token, $userId = null, $deviceInfo = [])
    {
        return self::updateOrCreate(
            ['device_token' => $token],
            [
                'user_id' => $userId,
                'device_type' => $deviceInfo['device_type'] ?? null,
                'device_name' => $deviceInfo['device_name'] ?? null,
                'app_version' => $deviceInfo['app_version'] ?? null,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Get active tokens for specific users
     */
    public static function getActiveTokens($userIds = null)
    {
        $query = self::where('is_active', true);
        
        if ($userIds) {
            $query->whereIn('user_id', is_array($userIds) ? $userIds : [$userIds]);
        }
        
        return $query->get();
    }

    /**
     * Deactivate old tokens
     */
    public static function deactivateOldTokens($days = 90)
    {
        return self::where('last_used_at', '<', now()->subDays($days))
            ->update(['is_active' => false]);
    }
}
