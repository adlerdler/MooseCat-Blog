<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * ProjectService - 项目服务类
 * 
 * 提供项目的管理功能，包括项目列表、创建、更新和删除操作。
 * Provides project management functionality, including project listing, creation, 
 * update and deletion operations.
 */
class ProjectService
{
    /**
     * 获取项目列表（带分页和筛选）
     * Get paginated project list with filters
     */
    public function getPaginatedProjects(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Project::query()
            ->with(['category', 'tags'])
            ->when($filters['category'] ?? null, fn($q, $slug) => $q->whereHas('category', fn($q) => $q->where('slug', $slug)))
            ->when($filters['status'] ?? 'published', fn($q, $status) => $q->where('status', $status))
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * 根据slug获取项目
     * Get project by slug
     */
    public function getProjectBySlug(string $slug): ?Project
    {
        return Project::where('slug', $slug)->with(['category', 'tags'])->first();
    }

    /**
     * 获取所有项目
     * Get all projects
     */
    public function getProjects(array $filters = []): Collection
    {
        return Project::query()
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->latest('created_at')
            ->get();
    }

    /**
     * 创建项目
     * Create project
     */
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

    /**
     * 更新项目
     * Update project
     */
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

    /**
     * 删除项目
     * Delete project
     */
    public function deleteProject(Project $project): bool
    {
        return DB::transaction(function () use ($project) {
            $project->tags()->detach();
            return $project->delete();
        });
    }
}