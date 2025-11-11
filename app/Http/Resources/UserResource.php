<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'role' => $this->role,
            'city' => new CityResource($this->whenLoaded('city')),
            'preferred_city_id' => $this->preferred_city_id,
            'is_verified' => $this->is_verified,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}