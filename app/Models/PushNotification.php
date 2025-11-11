<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'data',
        'type',
        'target',
        'target_ids',
        'image_url',
        'action_url',
        'scheduled_at',
        'sent_at',
        'sent_count',
        'success_count',
        'failure_count',
        'status',
        'created_by',
    ];

    protected $casts = [
        'data' => 'array',
        'target_ids' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the user who created this notification
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get notification logs
     */
    public function logs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    /**
     * Mark as sending
     */
    public function markAsSending()
    {
        $this->update(['status' => 'sending']);
    }

    /**
     * Mark as sent
     */
    public function markAsSent($sentCount, $successCount, $failureCount)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_count' => $sentCount,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
    }

    /**
     * Get pending notifications
     */
    public static function getPending()
    {
        return self::where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            })
            ->get();
    }
}
