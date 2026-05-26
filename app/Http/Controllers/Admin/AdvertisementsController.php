<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Advertisements Controller
 * 
 * Handles advertisement management operations.
 * Provides CRUD functionality for advertisements.
 */
class AdvertisementsController extends Controller
{
    protected $mockDataService;

    /**
     * Constructor
     * 
     * @param MockDataService $mockDataService
     */
    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    /**
     * Display the advertisement list
     * 
     * @return Response
     */
    public function index(): Response
    {
        $ads = $this->mockDataService->getAdvertisements();
        $adPositions = $this->mockDataService->getAdPositions();
        
        return Inertia::render('admin/Advertisements', [
            'ads' => $ads,
            'adPositions' => $adPositions,
        ]);
    }

    /**
     * Display the create advertisement form
     * 
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('admin/Advertisements');
    }

    /**
     * Store a newly created advertisement
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'url' => 'required|url',
            'image' => 'nullable|url',
        ]);

        return back()->with('success', '广告已创建');
    }

    /**
     * Update the specified advertisement
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        return back()->with('success', '广告已更新');
    }

    /**
     * Remove the specified advertisement
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        return back()->with('success', '广告已删除');
    }
}