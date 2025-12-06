<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? '',
            'description' => $this->description ?? '',
            'slug' => $this->slug ?? '',
            'pricing_type' => $this->pricing_type ?? 'negotiable',
            'price_from' => $this->price_from,
            'price_to' => $this->price_to,
            'base_price' => $this->base_price,
            'hourly_rate' => $this->hourly_rate,
            'minimum_charge' => $this->minimum_charge,
            'distance_rate' => $this->distance_rate,
            'currency' => $this->currency ?? 'EGP',
            'images' => $this->images ? collect($this->images)->map(fn($img) => asset('storage/' . $img))->values()->toArray() : [],
            'phone' => $this->phone ?? '',
            'whatsapp' => $this->whatsapp,
            'contact_phone' => $this->contact_phone,
            'contact_whatsapp' => $this->contact_whatsapp,
            'location' => $this->location,
            'address' => $this->address,
            'availability' => $this->availability,
            'vehicle_info' => $this->vehicle_info,
            'service_areas' => $this->service_areas ? (is_array($this->service_areas) ? $this->service_areas : []) : [],
            'requirements' => $this->requirements,
            'experience_years' => $this->experience_years,
            'is_active' => (bool) ($this->is_active ?? true),
            'is_verified' => (bool) ($this->is_verified ?? false),
            'is_featured' => (bool) ($this->is_featured ?? false),
            'featured_until' => $this->featured_until?->toISOString(),
            'rating' => $this->rating ? (float) round($this->rating, 1) : 0,
            'total_reviews' => (int) ($this->total_reviews ?? 0),
            'review_count' => (int) ($this->review_count ?? 0),
            'total_views' => (int) ($this->total_views ?? 0),
            'total_contacts' => (int) ($this->total_contacts ?? 0),
            'created_at' => $this->created_at?->toISOString() ?? '',
            'updated_at' => $this->updated_at?->toISOString() ?? '',
            'user' => $this->whenLoaded('user', function() {
                return $this->user ? (new UserResource($this->user))->resolve() : null;
            }),
            'serviceCategory' => $this->whenLoaded('serviceCategory', function() {
                return $this->serviceCategory ? (new ServiceCategoryResource($this->serviceCategory))->resolve() : null;
            }),
        ];
    }

    /**
     * Get analytics data for the service
     */
    private function getAnalyticsData(): array
    {
        return [
            'views_today' => method_exists($this->resource, 'getViewsToday') ? $this->getViewsToday() : 0,
            'contacts_today' => method_exists($this->resource, 'getContactsToday') ? $this->getContactsToday() : 0,
            'views_this_month' => method_exists($this->resource, 'getViewsThisMonth') ? $this->getViewsThisMonth() : 0,
            'contacts_this_month' => method_exists($this->resource, 'getContactsThisMonth') ? $this->getContactsThisMonth() : 0,
            'conversion_rate' => method_exists($this->resource, 'getConversionRate') ? $this->getConversionRate() : 0,
        ];
    }
}