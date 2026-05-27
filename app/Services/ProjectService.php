<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectService
{
    public function getPaginatedProjects(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Project::query()
            ->with(['category', 'tags'])
            ->when($filters['category'] ?? null, fn($q, $slug) => $q->whereHas('category', fn($q) => $q->where('slug', $slug)))
            ->when($filters['status'] ?? 'published', fn($q, $status) => $q->where('status', $status))
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function getProjectBySlug(string $slug): ?Project
    {
        return Project::where('slug', $slug)->with(['category', 'tags'])->first();
    }

    public function getProjects(array $filters = []): Collection
    {
        return Project::query()
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->latest('created_at')
            ->get();
    }

    public function createProject(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
            $project = Project::create($data);
            if (isset($data['tags'])) {
                $project->tags()->sync($data['tags']);
            }
            return $project;
        });
    }

    public function updateProject(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            if (isset($data['title']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }
            $project->update($data);
            if (isset($data['tags'])) {
                $project->tags()->sync($data['tags']);
            }
            return $project;
        });
    }

    public function deleteProject(Project $project): bool
    {
        return DB::transaction(function () use ($project) {
            $project->tags()->detach();
            return $project->delete();
        });
    }
}
