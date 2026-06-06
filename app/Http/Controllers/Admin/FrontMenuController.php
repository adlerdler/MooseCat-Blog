<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Menu;
use App\Services\CacheService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FrontMenuController extends Controller
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->middleware('permission:manage_menu');
        $this->cacheService = $cacheService;
    }
    public function index(): Response
    {
        $menus = Menu::orderBy('sort_order')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'type' => $m->type,
                'parent_id' => $m->parent_id,
                'parent_label' => $m->parent?->label_key,
                'label_key' => $m->label_key,
                'icon_name' => $m->icon_name,
                'path' => $m->path,
                'component_name' => $m->component_name,
                'sort_order' => $m->sort_order,
                'is_active' => $m->is_active,
            ]);

        return Inertia::render('admin/FrontMenu', [
            'allMenus' => $menus,
        ]);
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        Menu::create($request->validated());
        $this->cacheService->clearMenuCache();

        return back()->with('success', '菜单已创建');
    }

    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        $menu->update($request->validated());
        $this->cacheService->clearMenuCache();

        return back()->with('success', '菜单已更新');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();
        $this->cacheService->clearMenuCache();

        return back()->with('success', '菜单已删除');
    }

    public function batchUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'menus' => 'required|array',
            'menus.*.id' => 'nullable|integer',
            'menus.*.type' => 'required|string|in:front,admin',
            'menus.*.label_key' => 'required|string|max:255',
            'menus.*.path' => 'nullable|string|max:500',
            'menus.*.icon_name' => 'nullable|string|max:255',
            'menus.*.component_name' => 'nullable|string|max:255',
            'menus.*.parent_id' => 'nullable|integer',
            'menus.*.sort_order' => 'nullable|integer',
            'menus.*.is_active' => 'boolean',
        ]);

        foreach ($validated['menus'] as $menuData) {
            if (!empty($menuData['id'])) {
                $menu = Menu::find($menuData['id']);
                if ($menu) {
                    $menu->update($menuData);
                }
            } else {
                Menu::create($menuData);
            }
        }

        $this->cacheService->clearMenuCache();

        return back()->with('success', '菜单已保存');
    }
}