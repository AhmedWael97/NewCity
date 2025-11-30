<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceSponsorship extends Model
{
    protected $fillable = [
        'marketplace_item_id',
        'user_id',
        'package_type',
        'duration_days',
        'price_paid',
        'views_boost',
        'priority_level',
        'starts_at',
        'ends_at',
        'payment_method',
        'transaction_id',
        'payment_status',
        'status',
        'cancellation_reason',
        'views_gained',
        'contacts_gained',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'duration_days' => 'integer',
        'views_boost' => 'integer',
        'priority_level' => 'integer',
        'views_gained' => 'integer',
        'contacts_gained' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Sponsorship packages configuration
     */
    public static function packages(): array
    {
        return [
            'basic' => [
                'type' => 'basic',
                'name' => 'Basic Sponsorship',
                'name_ar' => 'رعاية أساسية',
                'duration' => 7,
                'duration_days' => 7,
                'price' => 50.00,
                'views_boost' => 100,
                'priority' => 3,
                'priority_level' => 3,
                'featured' => false,
                'features' => [
                    'Highlighted in search results',
                    '100 extra views',
                    '7 days duration',
                    'Basic badge',
                ],
                'features_ar' => [
                    'مميز في نتائج البحث',
                    '100 مشاهدة إضافية',
                    'مدة 7 أيام',
                    'شارة أساسية',
                ],
            ],
            'standard' => [
                'type' => 'standard',
                'name' => 'Standard Sponsorship',
                'name_ar' => 'رعاية قياسية',
                'duration' => 15,
                'duration_days' => 15,
                'price' => 90.00,
                'views_boost' => 250,
                'priority' => 6,
                'priority_level' => 6,
                'featured' => true,
                'features' => [
                    'Top placement in search',
                    '250 extra views',
                    '15 days duration',
                    'Premium badge',
                    'Featured on homepage',
                ],
                'features_ar' => [
                    'موضع أعلى في البحث',
                    '250 مشاهدة إضافية',
                    'مدة 15 يوم',
                    'شارة مميزة',
                    'عرض في الصفحة الرئيسية',
                ],
            ],
            'premium' => [
                'type' => 'premium',
                'name' => 'Premium Sponsorship',
                'name_ar' => 'رعاية بريميوم',
                'duration' => 30,
                'duration_days' => 30,
                'price' => 150.00,
                'views_boost' => 500,
                'priority' => 10,
                'priority_level' => 10,
                'featured' => false,
                'features' => [
                    'Top priority placement',
                    '500 extra views',
                    '30 days duration',
                    'VIP badge',
                    'Featured everywhere',
                    'Social media promotion',
                ],
                'features_ar' => [
                    'أولوية قصوى في الترتيب',
                    '500 مشاهدة إضافية',
                    'مدة 30 يوم',
                    'شارة VIP',
                    'عرض في كل مكان',
                    'ترويج على وسائل التواصل',
                ],
            ],
        ];
    }

    /**
     * Relationships
     */
    public function marketplaceItem(): BelongsTo
    {
        return $this->belongsTo(MarketplaceItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('ends_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
            ->where('ends_at', '<=', now());
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Check if sponsorship is currently active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && $this->ends_at > now()
            && $this->payment_status === 'completed';
    }

    /**
     * Check if sponsorship has expired
     */
    public function isExpired(): bool
    {
        return $this->ends_at <= now();
    }

    /**
     * Get days remaining
     */
    public function daysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->ends_at, false);
    }

    /**
     * Get ROI (Return on Investment) percentage
     */
    public function getRoi(): float
    {
        if ($this->price_paid <= 0) {
            return 0;
        }

        // Simple ROI based on contacts gained (assuming each contact has potential value)
        $estimatedValue = $this->contacts_gained * 10; // Assume each contact worth 10 currency units
        return (($estimatedValue - $this->price_paid) / $this->price_paid) * 100;
    }

    /**
     * Activate sponsorship
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'starts_at' => now(),
        ]);

        // Activate sponsorship on the marketplace item
        $this->marketplaceItem->activateSponsorship(
            $this->duration_days,
            $this->priority_level,
            $this->views_boost
        );
    }

    /**
     * Cancel sponsorship
     */
    public function cancel(string $reason): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Mark as expired and deactivate item sponsorship
     */
    public function expire(): void
    {
        $this->update(['status' => 'expired']);
        
        // Check if there are other active sponsorships for this item
        $hasOtherActive = static::where('marketplace_item_id', $this->marketplace_item_id)
            ->where('id', '!=', $this->id)
            ->active()
            ->exists();

        if (!$hasOtherActive) {
            $this->marketplaceItem->deactivateSponsorship();
        }
    }

    /**
     * Confirm payment
     */
    public function confirmPayment(string $transactionId): void
    {
        $this->update([
            'payment_status' => 'completed',
            'transaction_id' => $transactionId,
        ]);

        $this->activate();
    }
}
