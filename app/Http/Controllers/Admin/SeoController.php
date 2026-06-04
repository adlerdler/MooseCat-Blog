<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSeoRequest;
use App\Http\Requests\UpdateSeoRequest;
use App\Models\PageSeo;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SeoController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {
        $this->middleware('permission:manage_seo');
    }

    public function index(): Response
    {
        return Inertia::render('admin/SeoManager', [
            'globalSeo' => $this->seoService->getGlobalSeo(),
            'pageSeo'   => $this->seoService->getAllPageSeo(),
        ]);
    }

    public function update(UpdateSeoRequest $request)
    {
        $this->seoService->updateGlobalSeo($request->validated());

        return back()->with('success', 'SEO 设置已更新');
    }

    public function storePageSeo(StoreSeoRequest $request)
    {
        $this->seoService->createPageSeo($request->validated());

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

        $this->seoService->updatePageSeo($pageSeo, $validated);

        return back()->with('success', '页面 SEO 已更新');
    }

    public function destroyPageSeo(PageSeo $pageSeo)
    {
        $this->seoService->deletePageSeo($pageSeo);

        return back()->with('success', '页面 SEO 已删除');
    }
}
