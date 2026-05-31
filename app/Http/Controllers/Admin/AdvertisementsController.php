<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdvertisementRequest;
use App\Http\Requests\UpdateAdvertisementRequest;
use App\Models\AdPosition;
use App\Models\Advertisement;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdvertisementsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_ads');
    }
    public function index(): Response
    {
        $ads = Advertisement::with('adPosition')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($ad) => [
                'id' => $ad->id,
                'title' => $ad->title,
                'image_url' => $ad->image_url,
                'link_url' => $ad->link_url,
                'position_id' => $ad->position_id,
                'position_name' => $ad->adPosition?->label_key ?? $ad->adPosition?->name,
                'is_active' => $ad->is_active,
                'clicks_count' => $ad->clicks_count,
                'views_count' => $ad->views_count,
                'start_date' => $ad->start_date?->format('Y-m-d'),
                'end_date' => $ad->end_date?->format('Y-m-d'),
                'created_at' => $ad->created_at?->format('Y-m-d'),
            ]);

        $adPositions = AdPosition::orderBy('sort_order')->get(['id', 'name', 'label_key', 'is_active', 'sort_order']);

        return Inertia::render('admin/Advertisements', [
            'ads' => $ads,
            'adPositions' => $adPositions,
        ]);
    }

    public function store(StoreAdvertisementRequest $request): RedirectResponse
    {
        $data = $request->validated();
        Advertisement::create($data);

        return back()->with('success', '广告已创建');
    }

    public function update(UpdateAdvertisementRequest $request, Advertisement $advertisement): RedirectResponse
    {
        $data = $request->validated();
        $advertisement->update($data);

        return back()->with('success', '广告已更新');
    }

    public function destroy(Advertisement $advertisement): RedirectResponse
    {
        $advertisement->delete();

        return back()->with('success', '广告已删除');
    }
}