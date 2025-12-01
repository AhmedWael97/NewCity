<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ForumReport extends Model
{
    protected $fillable = [
        'user_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'description',
        'status',
        'reviewed_by',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewed($query)
    {
        return $query->whereIn('status', ['reviewed', 'resolved', 'dismissed']);
    }

    /**
     * Mark as reviewed
     */
    public function markAsReviewed($reviewerId, $notes = null)
    {
        $this->update([
            'status' => 'reviewed',
            'reviewed_by' => $reviewerId,
            'admin_notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Resolve report
     */
    public function resolve($reviewerId, $notes = null)
    {
        $this->update([
            'status' => 'resolved',
            'reviewed_by' => $reviewerId,
            'admin_notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Dismiss report
     */
    public function dismiss($reviewerId, $notes = null)
    {
        $this->update([
            'status' => 'dismissed',
            'reviewed_by' => $reviewerId,
            'admin_notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Get reason label
     */
    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'spam' => 'بريد عشوائي',
            'inappropriate' => 'محتوى غير مناسب',
            'offensive' => 'محتوى مسيء',
            'off_topic' => 'خارج الموضوع',
            'duplicate' => 'مكرر',
            'other' => 'أخرى',
            default => $this->reason,
        };
    }
}
