<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * 检查权限：Administrator 角色直接放行，其他用户需具备指定权限
     */
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, '未登录');
        }

        if ($user->isAdministrator()) {
            return $next($request);
        }

        // 其他用户检查具体权限
        abort_unless($user->hasPermissionTo($permission), 403, "无权限: {$permission}");

        return $next($request);
    }
}
