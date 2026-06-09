<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\AuthenticationException;

class CheckPermission
{
    /**
     * 检查权限：超级管理员通过 Gate::before 直接放行，其他用户需具备指定权限
     */
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        if (!Auth::check()) {
            throw new AuthenticationException('未登录');
        }

        // 借助 Laravel 原生 Gate 校验权限（管理员已在 AppServiceProvider Gate::before 中全局放行）
        Gate::authorize($permission);

        return $next($request);
    }
}
