<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Services\MenuService;
use App\Services\MockDataService;

class HandleInertiaRequests extends Middleware
{
    protected $menuService;
    protected $mockDataService;

    public function __construct(MenuService $menuService, MockDataService $mockDataService)
    {
        $this->menuService = $menuService;
        $this->mockDataService = $mockDataService;
    }

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $menus = [];
        $pageSeo = [];

        try {
            // 后台菜单使用真实数据并按权限过滤
            if ($user && ($request->is('admin') || $request->is('admin/*'))) {
                $menus = $this->menuService->getFilteredAdminMenus($user);
            } else {
                // 前台菜单使用模拟数据
                $menus = $this->mockDataService->getMenus();
            }
            $pageSeo = $this->mockDataService->getPageSeo();
        } catch (\Exception $e) {
            // 降级到模拟数据
            $menus = $this->mockDataService->getMenus();
            $pageSeo = $this->mockDataService->getPageSeo();
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'menus' => $menus,
            'pageSeo' => $pageSeo,
        ];
    }
}