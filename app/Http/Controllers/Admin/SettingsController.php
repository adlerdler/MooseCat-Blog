<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Settings Controller
 * 
 * Handles system settings management.
 * Provides functionality for managing site configuration, themes, SEO, and internationalization.
 */
class SettingsController extends Controller
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
     * Display the settings page
     * 
     * @return Response
     */
    public function index(): Response
    {
        $siteConfig = $this->mockDataService->getSiteConfig();
        $themes = $this->mockDataService->getThemes();
        $seoConfig = $this->mockDataService->getSeoConfig();
        $i18nConfig = $this->mockDataService->getI18nConfig();
        $media = $this->mockDataService->getMedia();

        return Inertia::render('admin/Settings', [
            'siteConfig' => $siteConfig,
            'themes' => $themes,
            'seoConfig' => $seoConfig,
            'i18nConfig' => $i18nConfig,
            'media' => $media,
        ]);
    }

    /**
     * Update system settings
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'site_logo' => 'nullable|url',
            'favicon' => 'nullable|url',
            'default_language' => 'required|string|max:10',
            'timezone' => 'required|string',
        ]);

        return back()->with('success', '设置已更新');
    }
}