<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Social Links Controller
 * 
 * Handles social link management operations.
 * Provides functionality for managing social media links.
 */
class SocialLinksController extends Controller
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
     * Display the social links page
     * 
     * @return Response
     */
    public function index(): Response
    {
        $footerConfig = $this->mockDataService->getFooterConfig();
        $siteConfig = $this->mockDataService->getSiteConfig();

        return Inertia::render('admin/SocialLinks', [
            'socialLinks' => $footerConfig['social_links'] ?? [],
            'navLinks' => $footerConfig['nav_links'] ?? [],
        ]);
    }

    /**
     * Update social links configuration
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'social_links' => 'required|array',
        ]);

        return back()->with('success', '社交链接已更新');
    }
}