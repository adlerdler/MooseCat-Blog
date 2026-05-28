<?php

namespace App\Services;

use App\Events\AdViewed;
use App\Models\AdPosition;
use App\Models\Advertisement;
use Illuminate\Support\Collection;

class AdService
{
    public function getAdsForPosition(string $positionKey, int $limit = 1): Collection
    {
        $position = AdPosition::where('name', $positionKey)
            ->where('is_active', true)
            ->first();

        if (!$position) {
            return collect();
        }

        $ads = Advertisement::where('position_id', $position->id)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('sort_order')
            ->get();

        if ($ads->isEmpty()) {
            return collect();
        }

        return $this->rotateAds($ads, $limit);
    }

    public function getAllActiveAds(): Collection
    {
        return Advertisement::with('adPosition')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('sort_order')
            ->get();
    }

    public function trackView(Advertisement $ad): void
    {
        event(new AdViewed($ad));
    }

    public function trackClick(Advertisement $ad): void
    {
        $ad->incrementClicks();
    }

    protected function rotateAds(Collection $ads, int $limit): Collection
    {
        if ($ads->count() <= $limit) {
            return $ads;
        }

        $totalWeight = $ads->sum(fn($ad) => $ad->weight ?? 1);
        $random = mt_rand(1, $totalWeight);
        $cumulative = 0;

        foreach ($ads as $ad) {
            $cumulative += $ad->weight ?? 1;
            if ($random <= $cumulative) {
                return collect([$ad]);
            }
        }

        return $ads->take($limit);
    }
}
