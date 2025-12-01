<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumPostVote extends Model
{
    protected $fillable = [
        'forum_post_id',
        'user_id',
        'is_helpful',
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($vote) {
            $vote->post->updateHelpfulCount();
        });

        static::deleted(function ($vote) {
            $vote->post->updateHelpfulCount();
        });
    }

    /**
     * Relationships
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
