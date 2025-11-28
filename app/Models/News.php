<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'thumbnail',
        'images',
        'category_id',
        'city_id',
        'is_active',
        'published_at',
        'views_count',
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
            if (empty($news->published_at) && $news->is_active) {
                $news->published_at = now();
            }
        });

        static::updating(function ($news) {
            if ($news->isDirty('title') && empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
        });

        static::deleting(function ($news) {
            // Delete thumbnail
            if ($news->thumbnail && Storage::disk('public')->exists($news->thumbnail)) {
                Storage::disk('public')->delete($news->thumbnail);
            }
            
            // Delete images
            if ($news->images) {
                foreach ($news->images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return Storage::url($this->thumbnail);
        }
        return asset('images/default-news.jpg');
    }

    public function getImagesUrlAttribute()
    {
        if ($this->images) {
            return array_map(function ($image) {
                return Storage::url($image);
            }, $this->images);
        }
        return [];
    }

    public function getExcerptAttribute()
    {
        return Str::limit(strip_tags($this->content), 150);
    }

    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200);
        return $minutes;
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }
}
