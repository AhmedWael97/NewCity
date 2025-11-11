<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'placement',
        'scope',
        'city_id',
        'target_categories',
        'target_demographics',
        'image_url',
        'video_url',
        'html_content',
        'click_url',
        'button_text',
        'pricing_model',
        'price_amount',
        'budget_total',
        'budget_daily',
        'spent_amount',
        'start_date',
        'end_date',
        'schedule_config',
        'status',
        'impressions',
        'clicks',
        'conversions',
        'ctr',
        'advertiser_id',
        'advertiser_name',
        'advertiser_email',
        'advertiser_phone',
        'company_name',
        'admin_notes',
        'reviewed_at',
        'reviewed_by'
    ];

    protected $casts = [
        'target_categories' => 'array',
        'target_demographics' => 'array',
        'schedule_config' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'reviewed_at' => 'datetime',
        'price_amount' => 'decimal:2',
        'budget_total' => 'decimal:2',
        'budget_daily' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'ctr' => 'decimal:2'
    ];

    // Relationships
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function advertiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advertiser_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    public function scopeForPlacement(Builder $query, string $placement): Builder
    {
        return $query->where('placement', $placement);
    }

    public function scopeForCity(Builder $query, ?int $cityId = null): Builder
    {
        return $query->where(function ($q) use ($cityId) {
            $q->where('scope', 'global')
              ->orWhere(function ($sq) use ($cityId) {
                  $sq->where('scope', 'city_specific')
                     ->where('city_id', $cityId);
              });
        });
    }

    public function scopeWithinBudget(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('budget_total')
              ->orWhereRaw('spent_amount < budget_total');
        });
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === 'active' &&
               $this->start_date <= now() &&
               ($this->end_date === null || $this->end_date >= now());
    }

    public function hasValidSchedule(): bool
    {
        if (!$this->schedule_config) {
            return true; // No schedule means always active
        }

        $now = now();
        $schedule = $this->schedule_config;

        // Check day of week
        if (isset($schedule['days'])) {
            $currentDay = strtolower($now->format('l'));
            if (!in_array($currentDay, $schedule['days'])) {
                return false;
            }
        }

        // Check hours
        if (isset($schedule['hours'])) {
            $currentHour = $now->hour;
            if ($currentHour < $schedule['hours']['start'] || $currentHour > $schedule['hours']['end']) {
                return false;
            }
        }

        return true;
    }

    public function withinBudget(): bool
    {
        if (!$this->budget_total) {
            return true;
        }

        return $this->spent_amount < $this->budget_total;
    }

    public function withinDailyBudget(): bool
    {
        if (!$this->budget_daily) {
            return true;
        }

        // Calculate today's spending
        $todaySpent = $this->calculateDailySpending();
        return $todaySpent < $this->budget_daily;
    }

    public function calculateDailySpending(): float
    {
        // This would need an ad_interactions table to track daily spending
        // For now, return 0
        return 0;
    }

    public function recordImpression(): void
    {
        $this->increment('impressions');
        $this->updateCTR();
    }

    public function recordClick(): void
    {
        $this->increment('clicks');
        $this->updateCTR();
        
        // Add cost based on pricing model
        if ($this->pricing_model === 'cpc') {
            $this->increment('spent_amount', (float) $this->price_amount);
        }
    }

    public function recordConversion(): void
    {
        $this->increment('conversions');
        
        // Add cost based on pricing model
        if ($this->pricing_model === 'cpa') {
            $this->increment('spent_amount', (float) $this->price_amount);
        }
    }

    protected function updateCTR(): void
    {
        if ($this->impressions > 0) {
            $ctr = ($this->clicks / $this->impressions) * 100;
            $this->update(['ctr' => round($ctr, 2)]);
        }
    }

    // Static helper methods
    public static function getActiveAdsForPlacement(string $placement, ?int $cityId = null): \Illuminate\Support\Collection
    {
        return static::active()
            ->forPlacement($placement)
            ->forCity($cityId)
            ->withinBudget()
            ->orderByRaw('CASE WHEN scope = "city_specific" THEN 1 ELSE 2 END') // City-specific ads first
            ->orderBy('price_amount', 'desc') // Higher paying ads first
            ->get()
            ->filter(function ($ad) {
                return $ad->hasValidSchedule() && $ad->withinDailyBudget();
            });
    }

    public static function getPricingTiers(): array
    {
        return [
            'global' => [
                'hero' => ['cpm' => 50, 'cpc' => 5, 'fixed_daily' => 500],
                'banner' => ['cpm' => 30, 'cpc' => 3, 'fixed_daily' => 300],
                'sidebar' => ['cpm' => 20, 'cpc' => 2, 'fixed_daily' => 200],
                'popup' => ['cpm' => 40, 'cpc' => 4, 'fixed_daily' => 400],
                'sponsored_listing' => ['cpc' => 2, 'cpa' => 20, 'fixed_monthly' => 1000]
            ],
            'city_specific' => [
                'hero' => ['cpm' => 30, 'cpc' => 3, 'fixed_daily' => 300],
                'banner' => ['cpm' => 20, 'cpc' => 2, 'fixed_daily' => 200],
                'sidebar' => ['cmp' => 15, 'cpc' => 1.5, 'fixed_daily' => 150],
                'popup' => ['cpm' => 25, 'cpc' => 2.5, 'fixed_daily' => 250],
                'sponsored_listing' => ['cpc' => 1, 'cpa' => 10, 'fixed_monthly' => 500]
            ]
        ];
    }
}