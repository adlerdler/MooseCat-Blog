<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SeoController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $seoConfig = $this->mockDataService->getSeoConfig();
        $pageSeo = $this->mockDataService->getPageSeo();
        
        return Inertia::render('admin/SeoManager', [
            'seoConfig' => $seoConfig,
            'pageSeo' => $pageSeo,
        ]);
    }

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