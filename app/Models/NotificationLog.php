<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'push_notification_id',
        'device_token_id',
        'status',
        'error_message',
        'opened_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
    ];

    /**
     * Get the push notification
     */
    public function pushNotification()
    {
        return $this->belongsTo(PushNotification::class);
    }

    /**
     * Get the device token
     */
    public function deviceToken()
    {
        return $this->belongsTo(DeviceToken::class);
    }

    /**
     * Mark as opened
     */
    public function markAsOpened()
    {
        $this->update([
            'status' => 'opened',
            'opened_at' => now(),
        ]);
    }
}
