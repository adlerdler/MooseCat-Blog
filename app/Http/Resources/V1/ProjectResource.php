<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'long_description' => $this->long_description,
            'client' => $this->client,
            'role' => $this->role,
            'year' => $this->year,
            'image' => $this->image,
            'url' => $this->url,
            'github_url' => $this->github_url,
            'technologies' => $this->technologies,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
