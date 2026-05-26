<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $posts = $this->mockDataService->getPosts(3);
        $projects = $this->mockDataService->getProjects(3);
        $videos = $this->mockDataService->getVideos(3);

        return Inertia::render('front/Home', [
            'posts' => $posts,
            'projects' => $projects,
            'videos' => $videos,
        ]);
    }
}