<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
        'type',
    ];

    /**
     * Boot the model
     */
    protected static function booted()
    {
        static::saved(function ($setting) {
            Cache::forget('app_settings');
            Cache::forget("app_setting_{$setting->key}");
        });

        static::deleted(function ($setting) {
            Cache::forget('app_settings');
            Cache::forget("app_setting_{$setting->key}");
        });
    }

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $value = Cache::remember("app_setting_{$key}", 3600, function () use ($key) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : null;
        });

        if ($value === null) {
            return $default;
        }

        // Parse based on type
        $setting = self::where('key', $key)->first();
        if ($setting) {
            return self::parseValue($value, $setting->type);
        }

        return $value;
    }

    /**
     * Set a setting value by key
     */
    public static function set($key, $value, $type = 'string')
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
            ]
        );
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAll()
    {
        return Cache::remember('app_settings', 3600, function () {
            $settings = self::all();
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->key] = self::parseValue($setting->value, $setting->type);
            }
            
            return $result;
        });
    }

    /**
     * Parse value based on type
     */
    protected static function parseValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Check if app is in maintenance mode
     */
    public static function isMaintenanceMode()
    {
        return self::get('maintenance_mode', false);
    }

    /**
     * Check if force update is enabled
     */
    public static function isForceUpdate()
    {
        return self::get('force_update', false);
    }

    /**
     * Get app name
     */
    public static function getAppName()
    {
        return self::get('app_name', 'City App');
    }

    /**
     * Check app version compatibility
     */
    public static function checkVersion($appVersion)
    {
        $minVersion = self::get('min_app_version', '1.0.0');
        $latestVersion = self::get('latest_app_version', '1.0.0');
        
        return [
            'is_compatible' => version_compare($appVersion, $minVersion, '>='),
            'needs_update' => version_compare($appVersion, $latestVersion, '<'),
            'force_update' => self::isForceUpdate() && version_compare($appVersion, $minVersion, '<'),
            'min_version' => $minVersion,
            'latest_version' => $latestVersion,
        ];
    }
}
