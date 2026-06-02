<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Services\MenuService;
use App\Services\MockDataService;
use App\Services\NotificationService;
use App\Services\SettingService;
use App\Services\I18nService;

class HandleInertiaRequests extends Middleware
{
    protected $menuService;
    protected $mockDataService;
    protected $notificationService;
    protected $settingService;
    protected $i18nService;

    public function __construct(
        MenuService $menuService,
        MockDataService $mockDataService,
        NotificationService $notificationService,
        SettingService $settingService,
        I18nService $i18nService,
    ) {
        $this->menuService = $menuService;
        $this->mockDataService = $mockDataService;
        $this->notificationService = $notificationService;
        $this->settingService = $settingService;
        $this->i18nService = $i18nService;
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
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $profile?->avatar ?? null,
                'slug' => $profile?->slug ?? null,
            ];
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

        // 动态获取前台语言列表
        $languages = [];
        try {
            $languages = $this->i18nService->getLanguages();
        } catch (\Exception $e) {
            // 降级：空列表
        }

        // 媒体库文件列表（供后台表单媒体选择器使用）
        $mediaFiles = [];
        if ($user && ($request->is('admin') || $request->is('admin/*'))) {
            try {
                $mediaFiles = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('collection_name', 'default')
                    ->latest()
                    ->get()
                    ->map(function ($m) {
                        $ext = pathinfo($m->file_name, PATHINFO_EXTENSION);
                        return [
                            'id'   => $m->uuid,
                            'name' => $m->name,
                            'type' => str_starts_with($m->mime_type ?? '', 'image/') ? 'image' : 'other',
                            'size' => $m->humanReadableSize ?? '0 B',
                            'url'  => "/media/{$m->uuid}" . ($ext ? ".{$ext}" : ''),
                            'date' => $m->created_at->format('Y-m-d'),
                        ];
                    })
                    ->toArray();
            } catch (\Exception $e) {
                // 降级：空列表
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
                'comment' => fn () => $request->session()->get('comment'),
            ],
            'menus' => $menus,
            'pageSeo' => $pageSeo,
            'siteConfig' => $siteConfig,
            'languages' => $languages,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'mediaFiles' => $mediaFiles,
        ];
    }
}