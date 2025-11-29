<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'thumbnail_url' => $this->thumbnail_url,
            'images_url' => $this->images_url,
            'views_count' => $this->views_count,
            'reading_time' => $this->reading_time,
            'published_at' => $this->published_at ? $this->published_at->toISOString() : null,
            'category' => $this->when($this->category, function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'city' => $this->when($this->city, function () {
                return [
                    'id' => $this->city->id,
                    'name' => $this->city->name,
                    'slug' => $this->city->slug ?? null,
                ];
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
