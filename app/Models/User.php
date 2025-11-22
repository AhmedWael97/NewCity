<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'user_role_id',
        'city_id',
        'preferred_city_id',
        'preferred_city_name',
        'user_type',
        'is_active',
        'is_verified',
        'address',
        'date_of_birth',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'date_of_birth' => 'date',
        ];
    }

    // User type constants
    const TYPE_REGULAR = 'regular';
    const TYPE_SHOP_OWNER = 'shop_owner';
    const TYPE_ADMIN = 'admin';

    /**
     * Get the full URL for the user avatar
     */
    public function getAvatarAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        
        return url('storage/' . $value);
    }

    /**
     * Get the user's city
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the user's preferred city
     */
    public function preferredCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'preferred_city_id');
    }

    /**
     * Get the user's role
     */
    public function userRole(): BelongsTo
    {
        return $this->belongsTo(UserRole::class);
    }

    /**
     * Get the user's shops
     */
    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    /**
     * Get the ratings that belong to the user
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get the support tickets created by the user
     */
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Get the support tickets assigned to the user (for admins)
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    /**
     * Get a specific rating for a shop by this user
     */
    public function ratingForShop(int $shopId)
    {
        return $this->ratings()->where('shop_id', $shopId)->first();
    }

    /**
     * Check if user has rated a specific shop
     */
    public function hasRatedShop(int $shopId): bool
    {
        return $this->ratings()->where('shop_id', $shopId)->exists();
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->userRole->slug === $role;
    }

    /**
     * Check if user is regular user
     */
    public function isRegular(): bool
    {
        return $this->user_type === self::TYPE_REGULAR;
    }

    /**
     * Check if user is shop owner
     */
    public function isShopOwner(): bool
    {
        return $this->user_type === self::TYPE_SHOP_OWNER;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::TYPE_ADMIN;
    }

    /**
     * Check if user is verified
     */
    public function isVerified(): bool
    {
        return $this->is_verified === true;
    }

    /**
     * Check if user has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return $this->userRole && $this->userRole->hasPermission($permission);
    }

    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the user's favorite shops
     */
    public function favoriteShops()
    {
        return $this->belongsToMany(Shop::class, 'shop_user_favorites')
            ->withTimestamps();
    }
}
