<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MarketplaceItem extends Model
{
    use SoftDeletes;

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = static::generateUniqueSlug($item->title);
            }
        });

        static::updating(function ($item) {
            if ($item->isDirty('title') && empty($item->slug)) {
                $item->slug = static::generateUniqueSlug($item->title);
            }
        });
    }

    /**
     * Generate a unique slug from the title
     */
    public static function generateUniqueSlug($title, $id = null)
    {
        $slug = \Illuminate\Support\Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        $query = static::withTrashed()->where('slug', $slug);
        if ($id) {
            $query->where('id', '!=', $id);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            $query = static::withTrashed()->where('slug', $slug);
            if ($id) {
                $query->where('id', '!=', $id);
            }
        }

        return $slug;
    }

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $fillable = [
        'user_id',
        'category_id',
        'city_id',
        'title',
        'slug',
        'description',
        'price',
        'condition',
        'images',
        'contact_phone',
        'contact_whatsapp',
        'status',
        'is_negotiable',
        'view_count',
        'max_views',
        'contact_count',
        'is_sponsored',
        'sponsored_until',
        'sponsored_priority',
        'sponsored_views_boost',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'is_negotiable' => 'boolean',
        'is_sponsored' => 'boolean',
        'view_count' => 'integer',
        'max_views' => 'integer',
        'contact_count' => 'integer',
        'sponsored_priority' => 'integer',
        'sponsored_views_boost' => 'integer',
        'sponsored_until' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function sponsorships(): HasMany
    {
        return $this->hasMany(MarketplaceSponsorship::class);
    }

    public function activeSponsorship()
    {
        return $this->hasOne(MarketplaceSponsorship::class)
            ->where('status', 'active')
            ->where('ends_at', '>', now());
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSponsored($query)
    {
        return $query->where('is_sponsored', true)
            ->where('sponsored_until', '>', now())
            ->orderByDesc('sponsored_priority')
            ->orderByDesc('created_at');
    }

    public function scopeNonSponsored($query)
    {
        return $query->where(function ($q) {
            $q->where('is_sponsored', false)
                ->orWhere('sponsored_until', '<=', now())
                ->orWhereNull('sponsored_until');
        });
    }

    public function scopeAvailableToView($query)
    {
        return $query->where(function ($q) {
            $q->where('is_sponsored', true) // Sponsored items always viewable
                ->orWhereRaw('view_count < max_views + sponsored_views_boost'); // Non-sponsored within limit
        });
    }

    public function scopeInCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Helper Methods
     */
    public function canBeViewed(): bool
    {
        if ($this->is_sponsored && $this->sponsored_until > now()) {
            return true; // Sponsored items always viewable
        }

        return $this->view_count < ($this->max_views + $this->sponsored_views_boost);
    }

    public function incrementViewCount(): void
    {
        if ($this->canBeViewed()) {
            $this->increment('view_count');
        }
    }

    public function incrementContactCount(): void
    {
        $this->increment('contact_count');
        
        // Also increment contact count on active sponsorship
        if ($sponsorship = $this->activeSponsorship) {
            $sponsorship->increment('contacts_gained');
        }
    }

    public function isSponsorshipActive(): bool
    {
        return $this->is_sponsored && $this->sponsored_until && $this->sponsored_until > now();
    }

    public function remainingViews(): int
    {
        $totalAllowed = $this->max_views + $this->sponsored_views_boost;
        $remaining = $totalAllowed - $this->view_count;
        
        return max(0, $remaining);
    }

    public function getSponsorshipDaysRemaining(): ?int
    {
        if (!$this->isSponsorshipActive()) {
            return null;
        }

        return now()->diffInDays($this->sponsored_until, false);
    }

    /**
     * Get images with full URLs
     */
    public function getImagesAttribute($value)
    {
        $images = is_string($value) ? json_decode($value, true) : $value;
        
        if (!is_array($images)) {
            return [];
        }

        return array_map(function($image) {
            if (!$image) {
                return null;
            }
            if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
                return $image;
            }
            return url('storage/' . $image);
        }, $images);
    }

    /**
     * Check if user owns this item
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Activate sponsorship
     */
    public function activateSponsorship(int $days, int $priority, int $viewsBoost): void
    {
        $this->update([
            'is_sponsored' => true,
            'sponsored_until' => now()->addDays($days),
            'sponsored_priority' => $priority,
            'sponsored_views_boost' => $this->sponsored_views_boost + $viewsBoost,
        ]);
    }

    /**
     * Deactivate sponsorship
     */
    public function deactivateSponsorship(): void
    {
        $this->update([
            'is_sponsored' => false,
            'sponsored_until' => null,
            'sponsored_priority' => 0,
        ]);
    }

    /**
     * Approve item
     */
    public function approve(): void
    {
        $this->update([
            'status' => 'active',
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject item
     */
    public function reject(string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_at' => null,
        ]);
    }

    /**
     * Mark as sold
     */
    public function markAsSold(): void
    {
        $this->update([
            'status' => 'sold',
        ]);
    }

    /**
     * Get the public URL for this item
     */
    public function getPublicUrl(): string
    {
        return route('marketplace.show', $this->id);
    }

    /**
     * Generate QR code for this item (SVG format)
     */
    public function generateQrCode(int $size = 200)
    {
        return \SimpleSoftwareIO\QrCode\Facades\QrCode::size($size)
            ->generate($this->getPublicUrl());
    }

    /**
     * Get QR code as base64 data URI for inline display
     */
    public function getQrCodeDataUri(int $size = 200): string
    {
        $qrCode = $this->generateQrCode($size);
        return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
    }
}
