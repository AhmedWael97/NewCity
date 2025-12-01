<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_id',
        'device_token',
        'device_type',
        'device_name',
        'os_version',
        'device_model',
        'device_manufacturer',
        'device_id',
        'app_version',
        'app_build_number',
        'language',
        'timezone',
        'is_active',
        'notifications_enabled',
        'ip_address',
        'device_metadata',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'notifications_enabled' => 'boolean',
        'device_metadata' => 'array',
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
     * Get the city associated with the device token
     */
    public function city()
    {
        return $this->belongsTo(City::class);
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
                'city_id' => $deviceInfo['city_id'] ?? null,
                'device_type' => $deviceInfo['device_type'] ?? null,
                'device_name' => $deviceInfo['device_name'] ?? null,
                'os_version' => $deviceInfo['os_version'] ?? null,
                'device_model' => $deviceInfo['device_model'] ?? null,
                'device_manufacturer' => $deviceInfo['device_manufacturer'] ?? null,
                'device_id' => $deviceInfo['device_id'] ?? null,
                'app_version' => $deviceInfo['app_version'] ?? null,
                'app_build_number' => $deviceInfo['app_build_number'] ?? null,
                'language' => $deviceInfo['language'] ?? 'ar',
                'timezone' => $deviceInfo['timezone'] ?? null,
                'notifications_enabled' => $deviceInfo['notifications_enabled'] ?? true,
                'ip_address' => $deviceInfo['ip_address'] ?? null,
                'device_metadata' => $deviceInfo['device_metadata'] ?? null,
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
