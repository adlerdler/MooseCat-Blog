<?php

declare(strict_types=1);

namespace App\Observers;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaObserver
{
    /**
     * Handle the Media "creating" event.
     * Auto-generate UUID before Spatie media record is saved.
     */
    public function creating(Media $media): void
    {
        if (empty($media->uuid)) {
            $media->uuid = (string) Str::uuid();
        }
    }
}
