<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSocialLinkRequest;
use App\Http\Requests\UpdateSocialLinkRequest;
use App\Models\FooterLink;
use App\Services\CacheService;
use App\Services\SocialLinksService;
use Inertia\Inertia;
use Inertia\Response;

class SocialLinksController extends Controller
{
    public function __construct(
        protected SocialLinksService $socialLinksService,
        protected CacheService $cacheService,
    ) {
        $this->middleware('permission:manage_social_links');
    }

    public function index(): Response
    {
        return Inertia::render('admin/SocialLinks', [
            'socialLinks' => $this->socialLinksService->getSocialLinks(),
            'navLinks' => [
                'categories' => $this->socialLinksService->getCategoryLinks(),
                'data'       => $this->socialLinksService->getDataLinks(),
            ],
        ]);
    }

    public function store(StoreSocialLinkRequest $request)
    {
        $validated = $request->validated();
        $validated['type'] = $request->input('type', 'social');

        $this->socialLinksService->create($validated);
        $this->cacheService->clearFooterCache();

        return redirect()->route('admin.social-links')->with('success', '链接已创建');
    }

    public function update(UpdateSocialLinkRequest $request, $id)
    {
        $footerLink = FooterLink::findOrFail($id);

        $this->socialLinksService->update($footerLink, $request->validated());
        $this->cacheService->clearFooterCache();

        return redirect()->route('admin.social-links')->with('success', '链接已更新');
    }

    public function destroy($id)
    {
        $footerLink = FooterLink::findOrFail($id);
        $this->socialLinksService->delete($footerLink);
        $this->cacheService->clearFooterCache();

        return redirect()->route('admin.social-links')->with('success', '链接已删除');
    }

    public function reorder()
    {
        $items = request()->validate(['items' => 'required|array', 'items.*.id' => 'required|integer', 'items.*.sort_order' => 'required|integer']);

        foreach ($items['items'] as $item) {
            FooterLink::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        $this->cacheService->clearFooterCache();

        return redirect()->route('admin.social-links')->with('success', '排序已更新');
    }
}
