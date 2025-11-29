<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PushNotificationResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->type,
            'target' => $this->target,
            'target_ids' => $this->target_ids,
            'image_url' => $this->image_url,
            'action_url' => $this->action_url,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at ? $this->scheduled_at->toISOString() : null,
            'sent_at' => $this->sent_at ? $this->sent_at->toISOString() : null,
            'total_sent' => $this->total_sent ?? 0,
            'total_opened' => $this->total_opened ?? 0,
            'total_failed' => $this->total_failed ?? 0,
            'creator' => $this->when($this->creator, function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
