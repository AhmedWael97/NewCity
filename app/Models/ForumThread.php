<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ForumThread extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'forum_category_id',
        'user_id',
        'city_id',
        'title',
        'slug',
        'body',
        'is_pinned',
        'is_locked',
        'is_approved',
        'status',
        'views_count',
        'replies_count',
        'last_post_user_id',
        'last_activity_at',
        'approved_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_approved' => 'boolean',
        'views_count' => 'integer',
        'replies_count' => 'integer',
        'last_activity_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($thread) {
            if (empty($thread->slug)) {
                $thread->slug = Str::slug($thread->title) . '-' . Str::random(6);
            }
            $thread->last_activity_at = now();
        });

        static::created(function ($thread) {
            $thread->category->updateCounters();
        });

        static::deleted(function ($thread) {
            $thread->category->updateCounters();
        });
    }

    /**
     * Relationships
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function lastPostUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_post_user_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ForumSubscription::class);
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

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('forum_category_id', $categoryId);
    }

    public function scopeInCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('last_activity_at');
    }

    /**
     * Increment view count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Update last activity
     */
    public function updateActivity($userId = null)
    {
        $this->update([
            'last_activity_at' => now(),
            'last_post_user_id' => $userId,
        ]);
    }

    /**
     * Update replies count
     */
    public function updateRepliesCount()
    {
        $this->update([
            'replies_count' => $this->posts()->where('status', 'active')->count(),
        ]);
    }

    /**
     * Check if user is subscribed
     */
    public function isSubscribedBy($userId): bool
    {
        return $this->subscriptions()->where('user_id', $userId)->exists();
    }

    /**
     * Check if user owns thread
     */
    public function isOwnedBy($userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Approve thread
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
     * Reject thread
     */
    public function reject()
    {
        $this->update([
            'is_approved' => false,
            'status' => 'rejected',
        ]);
    }

    /**
     * Lock/unlock thread
     */
    public function toggleLock()
    {
        $this->update(['is_locked' => !$this->is_locked]);
    }

    /**
     * Pin/unpin thread
     */
    public function togglePin()
    {
        $this->update(['is_pinned' => !$this->is_pinned]);
    }

    /**
     * Get route key name
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
