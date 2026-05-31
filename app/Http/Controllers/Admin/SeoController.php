<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seo;
use App\Models\PageSeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SeoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_seo');
    }
    public function index(): Response
    {
        $globalSeo = Seo::getGlobalSeo();
        $pageSeo = PageSeo::orderBy('page_key')->get();

        return Inertia::render('admin/SeoManager', [
            'globalSeo' => $globalSeo,
            'pageSeo' => $pageSeo,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
            'google_analytics' => 'nullable|string',
            'baidu_analytics' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'og_image' => 'nullable|url',
            'og_type' => 'nullable|string|max:50',
            'twitter_card' => 'nullable|string|max:50',
            'sitemap' => 'boolean',
            'robots' => 'boolean',
            'llm_txt' => 'boolean',
        ]);

        Seo::updateGlobalSeo($validated);

        return back()->with('success', 'SEO 设置已更新');
    }

    public function storePageSeo(Request $request)
    {
        $validated = $request->validate([
            'page_key' => 'required|string|unique:page_seo,page_key',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'og_image' => 'nullable|url',
        ]);

        PageSeo::create($validated);

        return back()->with('success', '页面 SEO 已添加');
    }

    public function updatePageSeo(Request $request, PageSeo $pageSeo)
    {
        $validated = $request->validate([
            'page_key' => 'required|string|unique:page_seo,page_key,' . $pageSeo->id,
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
        ]);

        $pageSeo->update($validated);

        return back()->with('success', '页面 SEO 已更新');
    }

    public function destroyPageSeo(PageSeo $pageSeo)
    {
        $pageSeo->delete();

        return back()->with('success', '页面 SEO 已删除');
    }
}
