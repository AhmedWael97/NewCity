<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminEmailQueue extends Model
{
    use HasFactory;

    protected $table = 'admin_email_queue';

    protected $fillable = [
        'event_type',
        'source',
        'subject',
        'body',
        'recipients',
        'event_data',
        'status',
        'attempts',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'recipients' => 'array',
        'event_data' => 'array',
        'sent_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    const EVENT_SHOP_SUGGESTION = 'shop_suggestion';
    const EVENT_CITY_SUGGESTION = 'city_suggestion';
    const EVENT_SHOP_RATE = 'shop_rate';
    const EVENT_SERVICE_RATE = 'service_rate';
    const EVENT_NEW_SERVICE = 'new_service';
    const EVENT_NEW_MARKETPLACE = 'new_marketplace';
    const EVENT_NEW_USER = 'new_user';

    /**
     * Scope for pending emails
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for failed emails
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Mark as sent
     */
    public function markAsSent()
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
            'error_message' => null,
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'attempts' => $this->attempts + 1,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Mark as processing
     */
    public function markAsProcessing()
    {
        $this->update([
            'status' => self::STATUS_PROCESSING,
        ]);
    }
}
