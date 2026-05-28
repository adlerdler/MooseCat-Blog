<?php

namespace App\Listeners;

use App\Events\AdViewed;

class TrackAdViewed
{
    public function handle(AdViewed $event): void
    {
        $event->advertisement->incrementViews();
    }
}
