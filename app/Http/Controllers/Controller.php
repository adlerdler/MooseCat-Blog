<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * 检查权限：超级管理员直接放行，其他用户查权限表。
     * 仅在方法体内直接调用时使用；构造函数中请用 '$this->middleware("permission:xxx")'。
     */
    protected function requirePermission(string $permission): void
    {
        if (!Auth::check()) {
            throw new \Illuminate\Auth\AuthenticationException('未登录');
        }

        // 借助 Laravel 原生 Gate 校验权限（管理员已在 AppServiceProvider Gate::before 中全局放行）
        \Illuminate\Support\Facades\Gate::authorize($permission);
    }
}
