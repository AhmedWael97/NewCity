<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeviceTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'device_token' => substr($this->device_token, 0, 20) . '...', // Truncate for security
            'device_type' => $this->device_type,
            'device_name' => $this->device_name,
            'app_version' => $this->app_version,
            'is_active' => $this->is_active,
            'user' => $this->when($this->user, function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'last_used_at' => $this->last_used_at ? $this->last_used_at->toISOString() : null,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
