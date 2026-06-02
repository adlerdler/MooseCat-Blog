<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $profile = $this->authorProfile;
        $socialLinks = $profile?->social_links ?? [];

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $profile?->avatar,
            'bio' => $profile?->bio,
            'display_name' => $profile?->display_name,
            'company' => $profile?->company,
            'social_links' => $socialLinks,
            'github' => $socialLinks['github'] ?? null,
            'twitter' => $socialLinks['twitter'] ?? null,
            'linkedin' => $socialLinks['linkedin'] ?? null,
            'role' => $this->role,
        ];
    }
}
