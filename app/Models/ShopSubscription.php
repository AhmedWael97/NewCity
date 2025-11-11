<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ShopSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'subscription_plan_id',
        'billing_cycle',
        'amount_paid',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'payment_method',
        'transaction_id',
        'payment_details',
        'cancellation_reason',
        'auto_renew',
        'next_billing_date'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'payment_details' => 'array',
        'auto_renew' => 'boolean',
        'next_billing_date' => 'datetime'
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->ends_at->isPast();
    }

    public function daysRemaining(): int
    {
        return $this->ends_at->diffInDays(now(), false);
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        return $this->isActive() && $this->daysRemaining() <= $days;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active' => '<span class="badge bg-success">نشط</span>',
            'expired' => '<span class="badge bg-danger">منتهي</span>',
            'cancelled' => '<span class="badge bg-warning">ملغي</span>',
            'pending' => '<span class="badge bg-info">في الانتظار</span>',
            default => '<span class="badge bg-secondary">غير معروف</span>'
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('ends_at', '<', now());
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('status', 'active')
                    ->where('ends_at', '<=', now()->addDays($days))
                    ->where('ends_at', '>', now());
    }
}