<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Services\MenuService;
use App\Services\MockDataService;
use App\Services\NotificationService;
use App\Services\SettingService;

class HandleInertiaRequests extends Middleware
{
    protected $menuService;
    protected $mockDataService;
    protected $notificationService;
    protected $settingService;

    public function __construct(
        MenuService $menuService,
        MockDataService $mockDataService,
        NotificationService $notificationService,
        SettingService $settingService,
    ) {
        $this->menuService = $menuService;
        $this->mockDataService = $mockDataService;
        $this->notificationService = $notificationService;
        $this->settingService = $settingService;
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
        $userData = null;

        if ($user) {
            $user->loadMissing('authorProfile');
            $profile = $user->authorProfile;
            $userArray = $user->toArray();
            $userData = array_merge(
                is_array($userArray) ? $userArray : $user->only(['id', 'name', 'email', 'avatar']),
                ['avatar' => $profile?->avatar ?? ($user->avatar ?? null)]
            );
        }

        $menus = [];
        $pageSeo = [];

        try {
            // 后台菜单使用真实数据并按权限过滤
            if ($user && ($request->is('admin') || $request->is('admin/*'))) {
                $menus = $this->menuService->getFilteredAdminMenus($user);
            } else {
                // 前台菜单从数据库读取真实数据
                $menus = \App\Models\Menu::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->toArray();
            }
            // 从 page_seo 表读取真实 SEO 数据
            $pageSeo = \App\Models\PageSeo::orderBy('page_key')->get()->toArray();
        } catch (\Exception $e) {
            // 降级到模拟数据
            $menus = $this->mockDataService->getMenus();
            $pageSeo = $this->mockDataService->getPageSeo();
        }

        $siteConfig = [];
        try {
            $siteConfig = $this->settingService->getSiteConfig();
        } catch (\Exception $e) {
            // 降级：空配置
        }

        // 通知铃铛数据：仅对已登录用户共享
        $notifications = [];
        $unreadCount = 0;
        if ($user) {
            try {
                $notifications = $this->notificationService->getBellNotifications($user);
                $unreadCount = $this->notificationService->getUnreadCount($user);
            } catch (\Exception $e) {
                // 降级：空通知
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $userData,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'menus' => $menus,
            'pageSeo' => $pageSeo,
            'siteConfig' => $siteConfig,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ];
    }
}