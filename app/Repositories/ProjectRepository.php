<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * ProjectRepository - 项目数据访问层
 * 
 * 封装项目相关的复杂查询逻辑，包括分页搜索、分类筛选、热门项目等功能。
 * Encapsulates complex query logic related to projects, including paginated search, 
 * category filtering, popular projects and other functionalities.
 */
class ProjectRepository
{
    /**
     * 创建新的数据访问层实例
     * Create a new repository instance
     */
    public function __construct()
    {
        //
    }

    /**
     * 获取分页项目列表（带筛选）
     * Get paginated projects with filters
     */
    public function getPaginatedProjects(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Project::query()
            ->with(['category', 'tags'])
            ->when($filters['category'] ?? null, fn($q, $slug) => $q->whereHas('category', fn($q) => $q->where('slug', $slug)))
            ->when($filters['tag'] ?? null, fn($q, $slug) => $q->whereHas('tags', fn($q) => $q->where('slug', $slug)))
            ->when($filters['status'] ?? 'published', fn($q, $status) => $q->where('status', $status))
            ->when($filters['keyword'] ?? null, function ($q) use ($filters) {
                $q->where(function ($q) use ($filters) {
                    $q->where('title', 'like', '%' . $filters['keyword'] . '%')
                      ->orWhere('description', 'like', '%' . $filters['keyword'] . '%');
                });
            })
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * 搜索项目
     * Search projects
     */
    public function searchProjects(string $keyword, int $limit = 10): Collection
    {
        return Project::query()
            ->with(['category'])
            ->where('status', 'published')
            ->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%');
            })
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * 根据分类获取项目
     * Get projects by category
     */
    public function getProjectsByCategory(string $categorySlug, int $limit = null): Collection
    {
        $query = Project::query()
            ->with(['tags'])
            ->whereHas('category', fn($q) => $q->where('slug', $categorySlug))
            ->where('status', 'published')
            ->latest('created_at');

        if ($limit !== null) {
            return $query->limit($limit)->get();
        }

        return $query->get();
    }

    /**
     * 获取热门项目
     * Get popular projects
     */
    public function getPopularProjects(int $limit = 10): Collection
    {
        return Project::query()
            ->with(['category'])
            ->where('status', 'published')
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 根据slug获取项目
     * Get project by slug
     */
    public function getProjectBySlug(string $slug): ?Project
    {
        return Project::where('slug', $slug)->with(['category', 'tags'])->first();
    }
}