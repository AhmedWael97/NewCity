<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'forum_thread_id',
        'email_notifications',
        'last_read_at',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'last_read_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'forum_thread_id');
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->update(['last_read_at' => now()]);
    }

    /**
     * Check if has unread posts
     */
    public function hasUnreadPosts(): bool
    {
        if (!$this->last_read_at) {
            return $this->thread->posts()->exists();
        }

        return $this->thread->posts()
            ->where('created_at', '>', $this->last_read_at)
            ->exists();
    }
}
