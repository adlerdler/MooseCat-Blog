<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckRegistrationEnabled - 检查注册开关
 *
 * 当 registration 设为 false 时，前台所有认证/个人中心页面返回 404。
 */
class CheckRegistrationEnabled
{
    public function __construct(
        protected SettingService $settingService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $registrationEnabled = (bool) $this->settingService->get('registration', true);

        if (! $registrationEnabled) {
            abort(404);
        }

        return $next($request);
    }
}
