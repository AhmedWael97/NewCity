<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ForumCategory extends Model
{
    protected $fillable = [
        'city_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'order',
        'is_active',
        'requires_approval',
        'threads_count',
        'posts_count',
        'last_activity_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
        'threads_count' => 'integer',
        'posts_count' => 'integer',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Relationships
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function threads(): HasMany
    {
        return $this->hasMany(ForumThread::class);
    }

    public function latestThread()
    {
        return $this->hasOne(ForumThread::class)->latestOfMany('last_activity_at');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInCity($query, $cityId)
    {
        return $query->where(function($q) use ($cityId) {
            $q->where('city_id', $cityId)->orWhereNull('city_id');
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Update category counters
     */
    public function updateCounters()
    {
        $this->update([
            'threads_count' => $this->threads()->where('status', 'active')->count(),
            'posts_count' => ForumPost::whereHas('thread', function($q) {
                $q->where('forum_category_id', $this->id)->where('status', 'active');
            })->where('status', 'active')->count(),
            'last_activity_at' => $this->threads()->where('status', 'active')->max('last_activity_at'),
        ]);
    }

    /**
     * Get route key name
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
