<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdvertisementsController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $ads = $this->mockDataService->getAdvertisements();
        
        return Inertia::render('admin/Advertisements', [
            'ads' => $ads,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Advertisements');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'url' => 'required|url',
            'image' => 'nullable|url',
        ]);

        return back()->with('success', '广告已创建');
    }

    public function update(Request $request, string $id)
    {
        return back()->with('success', '广告已更新');
    }

    public function destroy(string $id)
    {
        return back()->with('success', '广告已删除');
    }
}