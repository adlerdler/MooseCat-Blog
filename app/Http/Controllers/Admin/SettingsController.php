<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $siteConfig = $this->mockDataService->getSiteConfig();
        $themes = $this->mockDataService->getThemes();
        
        return Inertia::render('admin/Settings', [
            'siteConfig' => $siteConfig,
            'themes' => $themes,
        ]);
    }

    public function update(Request $request)
    {
        // 处理网站设置更新
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'site_logo' => 'nullable|url',
            'favicon' => 'nullable|url',
            'default_language' => 'required|string|max:10',
            'timezone' => 'required|string',
        ]);

        // 实际实现时保存到数据库
        return back()->with('success', '设置已更新');
    }
}