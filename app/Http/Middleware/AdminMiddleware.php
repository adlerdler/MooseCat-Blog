<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 检查用户是否已登录
        if (!Auth::check()) {
            return $this->redirectToLogin($request);
        }

        // 检查用户是否有管理员权限
        if (!$this->hasAdminPermission()) {
            return $this->redirectToHome($request);
        }

        return $next($request);
    }

    /**
     * 检查用户是否有管理员权限
     *
     * @return bool
     */
    protected function hasAdminPermission(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // 使用 Gate 进行权限检查
        if (Gate::forUser($user)->check('access-admin')) {
            return true;
        }

        // 检查用户是否有管理员角色（Spatie Permission）
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('admin') || $user->hasRole('administrator')) {
                return true;
            }
        }

        // 检查用户是否有管理权限
        if (method_exists($user, 'hasAnyPermission')) {
            if ($user->hasAnyPermission([
                'manage-users',
                'manage-roles',
                'manage-settings',
                'manage-posts',
                'manage-categories',
            ])) {
                return true;
            }
        }

        return false;
    }

    /**
     * 重定向到登录页面
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToLogin(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthorized',
                'redirect' => route('login')
            ], 401);
        }

        if (class_exists('\Inertia\Inertia')) {
            return Inertia::location(route('login'));
        }

        return Redirect::route('login');
    }

    /**
     * 重定向到首页（无权限）
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToHome(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Forbidden',
                'redirect' => route('home')
            ], 403);
        }

        if (class_exists('\Inertia\Inertia')) {
            return Inertia::location(route('home'));
        }

        return Redirect::route('home')->with([
            'error' => '您没有权限访问管理后台'
        ]);
    }
}