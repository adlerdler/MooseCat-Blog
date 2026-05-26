<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

class ProjectsController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $projects = $this->mockDataService->getProjects();

        return Inertia::render('front/Projects', [
            'projects' => $projects,
        ]);
    }

    public function show($id): Response
    {
        $projects = $this->mockDataService->getProjects();
        $project = collect($projects)->firstWhere('id', $id);

        return Inertia::render('front/ProjectDetail', [
            'project' => $project,
        ]);
    }
}