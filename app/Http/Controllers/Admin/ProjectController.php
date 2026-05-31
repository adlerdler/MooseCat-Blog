<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index(Request $request): Response
    {
        $filters = $request->only(['status', 'search']);
        
        $projects = Project::query()
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('year', 'desc')
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'description' => $project->description,
                    'long_description' => $project->long_description,
                    'client' => $project->client,
                    'role' => $project->role,
                    'year' => $project->year,
                    'image' => $project->image,
                    'url' => $project->url,
                    'github_url' => $project->github_url,
                    'technologies' => $project->technologies ?? [],
                    'status' => $project->status,
                    'sort_order' => $project->sort_order,
                    'views_count' => $project->views_count,
                    'likes_count' => $project->likes_count,
                    'created_at' => $project->created_at?->format('Y-m-d'),
                ];
            });

        return Inertia::render('admin/Projects', [
            'projects' => $projects,
            'filters' => $filters,
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        if (isset($data['technologies']) && is_string($data['technologies'])) {
            $data['technologies'] = array_map('trim', explode(',', $data['technologies']));
        }
        
        $this->projectService->createProject($data);

        return back()->with('success', '项目创建成功');
    }

    public function edit(Project $project): Response
    {
        return Inertia::render('admin/Projects', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'long_description' => $project->long_description,
                'client' => $project->client,
                'role' => $project->role,
                'year' => $project->year,
                'image' => $project->image,
                'url' => $project->url,
                'github_url' => $project->github_url,
                'technologies' => $project->technologies ? implode(', ', $project->technologies) : '',
                'status' => $project->status,
                'sort_order' => $project->sort_order,
            ],
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $data = $request->validated();
        
        if (isset($data['technologies']) && is_string($data['technologies'])) {
            $data['technologies'] = array_map('trim', explode(',', $data['technologies']));
        }
        
        $this->projectService->updateProject($project, $data);

        return back()->with('success', '项目更新成功');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->projectService->deleteProject($project);

        return back()->with('success', '项目删除成功');
    }
}