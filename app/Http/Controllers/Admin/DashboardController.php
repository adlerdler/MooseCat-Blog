<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    /**
     * Constructor
     *
     * @param MockDataService $mockDataService
     */
    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    /**
     * Display the admin dashboard
     *
     * @return Response
     */
    public function index(): Response
    {
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

            return redirect()->intended(route('admin'));
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

        return redirect()->route('login');
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
}