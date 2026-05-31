<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SettingService;
use Inertia\Inertia;

/**
 * CheckMaintenanceMode - 维护模式中间件
 * 
 * 当后台开启维护模式时，阻塞非管理员用户的前台访问，
 * 显示维护模式页面。管理员用户可正常访问。
 */
class CheckMaintenanceMode
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function handle(Request $request, Closure $next)
    {
        // 绕过 admin 路由，让管理员能正常访问
        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        // 直接查库，绕过缓存
        $maintenance = \App\Models\Setting::first()?->maintenance ?? false;

        if ($maintenance) {
            $siteConfig = $this->settingService->getSiteConfig();

            return Inertia::render('front/MaintenanceMode', [
                'siteConfig' => $siteConfig,
            ])->toResponse($request);
        }

        return $next($request);
    }
}
