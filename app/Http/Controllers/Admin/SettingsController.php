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
        
        $mediaItems = \App\Models\Media::with('media')
            ->latest()
            ->get()
            ->map(function ($item) {
                $file = $item->media->first();
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'name' => $file?->file_name ?? $item->title ?? 'Untitled',
                    'type' => str_starts_with($file?->mime_type ?? '', 'image/') ? 'image' : 
                             (str_starts_with($file?->mime_type ?? '', 'video/') ? 'video' : 'document'),
                    'size' => $file ? $this->formatSize($file?->size ?? 0) : '0 B',
                    'url' => $file?->getUrl() ?? '',
                    'date' => $item->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('admin/Settings', [
            'siteConfig' => $siteConfig,
            'themes' => $themes,
            'seoConfig' => $seoConfig,
            'i18nConfig' => $i18nConfig,
            'media' => $mediaItems,
        ]);
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        $k = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
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