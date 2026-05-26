<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * SEO Controller
 * 
 * Handles SEO management operations.
 * Provides functionality for managing SEO settings and page meta data.
 */
class SeoController extends Controller
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
     * Display the SEO management page
     * 
     * @return Response
     */
    public function index(): Response
    {
        $seoConfig = $this->mockDataService->getSeoConfig();
        $pageSeo = $this->mockDataService->getPageSeo();
        
        return Inertia::render('admin/SeoManager', [
            'seoConfig' => $seoConfig,
            'pageSeo' => $pageSeo,
        ]);
    }

    /**
     * Update SEO configuration
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'robots' => 'nullable|string',
        ]);

        return back()->with('success', 'SEO设置已更新');
    }
}