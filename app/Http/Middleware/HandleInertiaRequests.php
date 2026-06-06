<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Services\CacheService;
use App\Services\MenuService;
use App\Services\NotificationService;
use App\Services\SettingService;
use App\Services\I18nService;

class HandleInertiaRequests extends Middleware
{
    protected $menuService;
    protected $notificationService;
    protected $settingService;
    protected $i18nService;
    protected CacheService $cacheService;

    public function __construct(
        MenuService $menuService,
        NotificationService $notificationService,
        SettingService $settingService,
        I18nService $i18nService,
        CacheService $cacheService,
    ) {
        $this->menuService = $menuService;
        $this->notificationService = $notificationService;
        $this->settingService = $settingService;
        $this->i18nService = $i18nService;
        $this->cacheService = $cacheService;
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
            // 降级到空数据
            $menus = [];
            $pageSeo = [];
        }

        $siteConfig = [];
        try {
            $siteConfig = $this->settingService->getSiteConfig();
        } catch (\Exception $e) {
            // 降级：空配置
        }

        // 全局共享配置（所有页面）
        $footerConfig = [];
        $themes = [];
        try {
            $footerConfig = $this->cacheService->remember('footer_config', function () {
                $allLinks = \App\Models\FooterLink::active()
                    ->whereIn('type', ['social_link', 'nav_link'])
                    ->get();

                $socialLinks = $allLinks
                    ->where('type', 'social_link')
                    ->map(function ($link) {
                        return [
                            'id' => $link->id,
                            'platform' => $link->platform,
                            'url' => $link->url,
                            'icon_name' => $link->icon_name ?? $link->icon,
                            'label' => $link->label,
                            'sort_order' => $link->sort_order,
                            'is_active' => $link->is_active,
                        ];
                    })
                    ->values()
                    ->toArray();

                $categoryLinks = $allLinks
                    ->where('type', 'nav_link')
                    ->where('platform', 'categories')
                    ->map(function ($link) {
                        return [
                            'id' => $link->id,
                            'label' => $link->label,
                            'url' => $link->url,
                            'sort_order' => $link->sort_order,
                            'is_active' => $link->is_active,
                        ];
                    })
                    ->values()
                    ->toArray();

                $dataLinks = $allLinks
                    ->where('type', 'nav_link')
                    ->where('platform', 'data')
                    ->map(function ($link) {
                        return [
                            'id' => $link->id,
                            'label' => $link->label,
                            'url' => $link->url,
                            'sort_order' => $link->sort_order,
                            'is_active' => $link->is_active,
                        ];
                    })
                    ->values()
                    ->toArray();

                return [
                    'social_links' => $socialLinks,
                    'nav_links' => [
                        'categories' => $categoryLinks,
                        'data' => $dataLinks,
                    ],
                ];
            });

            $themes = $this->cacheService->remember('themes_list', function () {
                return \App\Models\Theme::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->map(fn($t) => [
                        'id' => $t->id,
                        'name' => $t->name,
                        'label' => $t->label,
                        'color' => $t->color,
                        'sort_order' => $t->sort_order,
                        'is_active' => $t->is_active,
                        'is_default' => $t->is_default,
                        'preview_image' => $t->preview_image,
                    ])
                    ->toArray();
            });
        } catch (\Exception $e) {
            // 降级：空数组
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

        // 前台广告数据（所有页面共享）
        $frontAds = [];
        $frontAdPositions = [];
        try {
            $frontAds = \App\Models\Advertisement::with('adPosition')
                ->where('is_active', true)
                ->get()
                ->map(fn($ad) => [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'image_url' => $ad->image_url,
                    'link_url' => $ad->link_url,
                    'position_id' => $ad->position_id,
                    'position_name' => $ad->adPosition?->name,
                    'is_active' => $ad->is_active,
                    'clicks_count' => $ad->clicks_count,
                    'views_count' => $ad->views_count,
                    'start_date' => $ad->start_date?->format('Y-m-d'),
                    'end_date' => $ad->end_date?->format('Y-m-d'),
                ]);
            $frontAdPositions = \App\Models\AdPosition::orderBy('sort_order')
                ->get(['id', 'name', 'label_key', 'is_active', 'sort_order'])
                ->toArray();
        } catch (\Exception $e) {
            // 降级：空数组
        }

        return [
            ...parent::share($request),
            'csrf_token' => csrf_token(),
            'auth' => [
                'user' => $userData,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'comment' => fn () => $request->session()->get('comment'),
                'user_not_found' => fn () => $request->session()->get('user_not_found'),
            ],
            'menus' => $menus,
            'pageSeo' => $pageSeo,
            'siteConfig' => $siteConfig,
            'footerConfig' => $footerConfig,
            'themes' => $themes,
            'languages' => $languages,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'mediaFiles' => $mediaFiles,
            'frontAds' => $frontAds,
            'frontAdPositions' => $frontAdPositions,
        ];
    }
}