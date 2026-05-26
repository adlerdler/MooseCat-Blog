<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Project Controller
 * 
 * Handles project management operations.
 * Provides CRUD functionality for projects.
 */
class ProjectController extends Controller
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
     * Display a listing of the projects.
     * 
     * @return Response
     */
    public function index(): Response
    {
        $projects = $this->mockDataService->getProjects();
        
        return Inertia::render('admin/Projects', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new project.
     * 
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('admin/Projects');
    }

    /**
     * Store a newly created project in storage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Handle project creation
    }

    /**
     * Display the specified project.
     * 
     * @param string $id
     */
    public function show(string $id)
    {
        // Show project details
    }

    /**
     * Show the form for editing the specified project.
     * 
     * @param string $id
     * @return Response
     */
    public function edit(string $id): Response
    {
        $projects = $this->mockDataService->getProjects();
        $project = collect($projects)->firstWhere('id', $id);
        
        return Inertia::render('admin/Projects', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified project in storage.
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        // Handle project update
    }

    /**
     * Remove the specified project from storage.
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        // Handle project deletion
    }
}
