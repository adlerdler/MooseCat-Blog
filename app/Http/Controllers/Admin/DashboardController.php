<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $posts = $this->mockDataService->getPosts();
        $projects = $this->mockDataService->getProjects();
        $videos = $this->mockDataService->getVideos();
        $users = $this->mockDataService->getUsers();
        $logs = $this->mockDataService->getLogs();
        
        return Inertia::render('admin/Index', [
            'posts' => $posts,
            'projects' => $projects,
            'videos' => $videos,
            'users' => $users,
            'logs' => $logs,
        ]);
    }

    public function login(): Response
    {
        return Inertia::render('admin/Login');
    }

    public function handleLogin()
    {
        return redirect()->route('admin');
    }

    public function about(): Response
    {
        return Inertia::render('admin/About');
    }
}