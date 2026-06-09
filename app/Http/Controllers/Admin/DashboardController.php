<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\CaptchaService;
use App\Services\DashboardService;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Dashboard Controller
 *
 * Handles admin dashboard related operations.
 * Provides data for the admin dashboard overview page.
 */
class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected MenuService $menuService;
    protected CaptchaService $captchaService;

    /**
     * Constructor
     *
     * @param DashboardService $dashboardService
     * @param MenuService $menuService
     * @param CaptchaService $captchaService
     */
    public function __construct(DashboardService $dashboardService, MenuService $menuService, CaptchaService $captchaService)
    {
        $this->dashboardService = $dashboardService;
        $this->menuService = $menuService;
        $this->captchaService = $captchaService;
    }

    /**
     * Display the admin dashboard
     *
     * @return Response
     */
    public function index(): Response
    {
        $this->requirePermission('view_analytics');

        return Inertia::render('admin/Index', $this->dashboardService->getDashboardData());
    }

    /**
     * Display the login page
     */
    public function login()
    {
        // 已登录用户访问登录页 → 引导跳转
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdministrator() || $user->hasPermissionTo('view_analytics')) {
                return redirect('/admin/index');
            }

            // 智能跳转到该用户第一个可访问的后台页面（例如：文章管理、分类管理等）
            $menus = $this->menuService->getFilteredAdminMenus($user);
            $firstPath = $this->findFirstMenuPath($menus);

            if ($firstPath) {
                return redirect($firstPath);
            }

            return redirect('/');
        }

        return Inertia::render('admin/Login', [
            'captcha' => $this->captchaService->create(),
        ]);
    }

    /**
     * Handle login request
     */
    public function handleLogin(LoginRequest $request)
    {
        if (Auth::attempt($request->credentials(), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // 检查用户是否被禁用
            if ($user->status === 'inactive') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'disabled' => '该用户已被禁用，请联系管理员',
                ])->onlyInput('email');
            }

            // Admin 或拥有 dashboard 权限的用户 → 跳转仪表盘
            if ($user->isAdministrator() || $user->hasPermissionTo('view_analytics')) {
                return redirect('/admin/index');
            }

            // 其他用户 → 智能跳转到第一个可访问的后台页面
            $menus = $this->menuService->getFilteredAdminMenus($user);
            $firstPath = $this->findFirstMenuPath($menus);

            if ($firstPath) {
                return redirect($firstPath);
            }

            // 没有任何可访问页面 → 拒绝登录
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => '无后台访问权限',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => '邮箱或密码错误',
        ])->onlyInput('email');
    }

    /**
     * Handle logout request
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    /**
     * Display the about page
     *
     * @return Response
     */
    public function about(): Response
    {
        $this->requirePermission('view_analytics');
        return Inertia::render('admin/About');
    }

    /**
     * 从过滤后的菜单树中查找第一个有效路径
     *
     * @param array $menus
     * @return string|null
     */
    protected function findFirstMenuPath(array $menus): ?string
    {
        foreach ($menus as $menu) {
            if (!empty($menu['path'])) {
                return $menu['path'];
            }
            if (!empty($menu['children'])) {
                $found = $this->findFirstMenuPath($menu['children']);
                if ($found) {
                    return $found;
                }
            }
        }
        return null;
    }
}