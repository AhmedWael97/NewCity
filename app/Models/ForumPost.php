<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'forum_thread_id',
        'user_id',
        'parent_id',
        'body',
        'is_approved',
        'status',
        'helpful_count',
        'approved_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'helpful_count' => 'integer',
        'approved_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($post) {
            $post->thread->updateRepliesCount();
            $post->thread->updateActivity($post->user_id);
            $post->thread->category->updateCounters();
        });

        static::deleted(function ($post) {
            $post->thread->updateRepliesCount();
            $post->thread->category->updateCounters();
        });
    }

    /**
     * Relationships
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'forum_thread_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'parent_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ForumPostVote::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(ForumReport::class, 'reportable');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending')->where('is_approved', false);
    }

    public function scopeInThread($query, $threadId)
    {
        return $query->where('forum_thread_id', $threadId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Check if user voted
     */
    public function isVotedByUser($userId): bool
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    /**
     * Check if user owns post
     */
    public function isOwnedBy($userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Approve post
     */
    public function approve()
    {
        $this->update([
            'is_approved' => true,
            'status' => 'active',
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject post
     */
    public function reject()
    {
        $this->update([
            'is_approved' => false,
            'status' => 'rejected',
        ]);
    }

    /**
     * Update helpful count
     */
    public function updateHelpfulCount()
    {
        $this->update([
            'helpful_count' => $this->votes()->where('is_helpful', true)->count(),
        ]);
    }
}
