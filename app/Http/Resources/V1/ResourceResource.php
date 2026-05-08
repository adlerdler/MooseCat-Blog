<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'title' => $this->title,
            'description' => $this->description,
            'format' => $this->format,
            'file_size' => $this->file_size,
            'image' => $this->image,
            'direct_link' => $this->direct_link,
            'drives' => $this->drives,
            'downloads_count' => $this->downloads_count,
            'likes_count' => $this->likes_count,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
