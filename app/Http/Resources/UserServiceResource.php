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
            'title' => $this->title,
            'description' => $this->description,
            'pricing_type' => $this->pricing_type,
            'price_from' => $this->price_from,
            'price_to' => $this->price_to,
            'currency' => $this->currency,
            'images' => $this->images ? collect($this->images)->map(fn($img) => asset('storage/' . $img)) : [],
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'location' => $this->location,
            'address' => $this->address,
            'availability' => $this->availability,
            'service_areas' => $this->service_areas,
            'requirements' => $this->requirements,
            'is_active' => $this->is_active,
            'is_verified' => $this->is_verified,
            'is_featured' => $this->is_featured,
            'featured_until' => $this->featured_until?->toISOString(),
            'rating' => round($this->rating ?? 0, 1),
            'total_reviews' => $this->total_reviews ?? 0,
            'total_views' => $this->total_views ?? 0,
            'total_contacts' => $this->total_contacts ?? 0,
            'last_active' => $this->last_active?->toISOString(),
            'user' => new UserResource($this->whenLoaded('user')),
            'city' => new CityResource($this->whenLoaded('city')),
            'service_category' => new ServiceCategoryResource($this->whenLoaded('serviceCategory')),
            'reviews' => ServiceReviewResource::collection($this->whenLoaded('reviews')),
            'analytics' => $this->when(
                $request->user() && $request->user()->id === $this->user_id,
                $this->getAnalyticsData()
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get analytics data for the service
     */
    private function getAnalyticsData(): array
    {
        return [
            'views_today' => $this->getViewsToday(),
            'contacts_today' => $this->getContactsToday(),
            'views_this_month' => $this->getViewsThisMonth(),
            'contacts_this_month' => $this->getContactsThisMonth(),
            'conversion_rate' => $this->getConversionRate(),
        ];
    }
}