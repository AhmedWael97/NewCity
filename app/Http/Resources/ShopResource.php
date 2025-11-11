<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'address' => $this->address,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'website' => $this->website,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'opening_hours' => $this->opening_hours,
            'is_open_now' => $this->is_open_now,
            'is_verified' => $this->is_verified,
            'is_featured' => $this->is_featured,
            'featured_until' => $this->featured_until?->toISOString(),
            'average_rating' => round($this->average_rating ?? 0, 1),
            'total_ratings' => $this->total_ratings ?? 0,
            'total_views' => $this->total_views ?? 0,
            'images' => $this->images ? collect($this->images)->map(fn($img) => asset('storage/' . $img)) : [],
            'logo' => $this->logo ? asset('storage/' . $this->logo) : null,
            'banner_image' => $this->banner_image ? asset('storage/' . $this->banner_image) : null,
            'delivery_available' => $this->delivery_available,
            'delivery_fee' => $this->delivery_fee,
            'minimum_order' => $this->minimum_order,
            'estimated_delivery_time' => $this->estimated_delivery_time,
            'payment_methods' => $this->payment_methods,
            'city' => new CityResource($this->whenLoaded('city')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'user' => new UserResource($this->whenLoaded('user')),
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
            'distance_km' => $this->when(isset($this->distance_km), round($this->distance_km ?? 0, 2)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}