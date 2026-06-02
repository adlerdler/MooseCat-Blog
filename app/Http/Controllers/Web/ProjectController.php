<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\VisitService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        protected VisitService $visitService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'tag']);
        
        $projects = Project::query()
            ->with('tags')
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['tag'] ?? null, function ($query, $tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('slug', $tag);
                });
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('year', 'desc')
            ->paginate(9);

        return view('projects.index', compact('projects', 'filters'));
    }

    public function show(Project $project): View
    {
        $project->load('tags');
        $this->visitService->trackModel($project, $request);
        return view('projects.show', compact('project'));
    }
}
