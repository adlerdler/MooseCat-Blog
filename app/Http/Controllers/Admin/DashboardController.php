<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MenuService;
use App\Services\MockDataService;
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
    protected $mockDataService;
    protected $menuService;

    /**
     * Constructor
     *
     * @param MockDataService $mockDataService
     * @param MenuService $menuService
     */
    public function __construct(MockDataService $mockDataService, MenuService $menuService)
    {
        $this->mockDataService = $mockDataService;
        $this->menuService = $menuService;
    }

    /**
     * Display the admin dashboard
     *
     * @return Response
     */
    public function index(): Response
    {
        $this->requirePermission('view_analytics');
        $posts = $this->mockDataService->getPosts();
        $projects = $this->mockDataService->getProjects();
        $videos = $this->mockDataService->getVideos();
        $users = $this->mockDataService->getUsers();
        $logs = $this->mockDataService->getLogs();
        $categories = $this->mockDataService->getCategories();
        $comments = $this->mockDataService->getComments();
        $resources = $this->mockDataService->getResources();
        $visits = $this->mockDataService->getVisits();
        $userLevels = $this->mockDataService->getUserLevels();
        $roles = $this->mockDataService->getRoles();
        $tags = $this->mockDataService->getTags();
        $taggables = $this->mockDataService->getTagsables();

        return Inertia::render('admin/Index', [
            'posts' => $posts,
            'projects' => $projects,
            'videos' => $videos,
            'users' => $users,
            'logs' => $logs,
            'categories' => $categories,
            'comments' => $comments,
            'resources' => $resources,
            'visits' => $visits,
            'userLevels' => $userLevels,
            'roles' => $roles,
            'tags' => $tags,
            'taggables' => $taggables,
        ]);
    }

    /**
     * Display the login page
     *
     * @return Response
     */
    public function login(): Response
    {
        return Inertia::render('admin/Login');
    }

    /**
     * Handle login request
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Admin 或拥有 dashboard 权限的用户 → 跳转仪表盘
            if ($user->hasRole('Administrator') || $user->hasPermissionTo('view_analytics')) {
                return redirect()->intended(route('admin'));
            }

            // 其他用户 → 智能跳转到第一个可访问的后台页面
            $menus = $this->menuService->getFilteredAdminMenus($user);
            $firstPath = $this->findFirstMenuPath($menus);

            if ($firstPath) {
                return redirect()->intended($firstPath);
            }

            // 没有任何可访问页面 → 跳转 403
            return redirect()->intended(route('admin.forbidden'));
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