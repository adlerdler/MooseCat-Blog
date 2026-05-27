<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

/**
 * CategoryService - 分类服务类
 * 
 * 提供文章分类的管理功能，包括分类列表、树形结构、创建、更新和删除操作。
 * Provides category management functionality, including category listing, tree structure, 
 * creation, update and deletion operations.
 */
class CategoryService
{
    /**
     * 获取所有分类（带文章数量）
     * Get all categories with post counts
     */
    public function getCategories(): Collection
    {
        return Category::query()
            ->withCount('posts')
            ->orderBy('order', 'asc')
            ->get();
    }

    /**
     * 获取分类树形结构
     * Get category tree structure
     */
    public function getCategoryTree(): Collection
    {
        return Category::query()
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->withCount('posts')->orderBy('order', 'asc');
            }])
            ->orderBy('order', 'asc')
            ->get();
    }

    /**
     * 根据slug获取分类
     * Get category by slug
     */
    public function getCategoryBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    /**
     * 创建分类
     * Create category
     */
    public function createCategory(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
            return Category::create($data);
        });
    }

    /**
     * 更新分类
     * Update category
     */
    public function updateCategory(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            if (isset($data['name']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            $category->update($data);
            return $category;
        });
    }

    /**
     * 删除分类
     * Delete category
     */
    public function deleteCategory(Category $category): bool
    {
        return DB::transaction(function () use ($category) {
            $defaultCategory = Category::where('slug', 'uncategorized')->first();
            if ($defaultCategory && $defaultCategory->id !== $category->id) {
                $category->posts()->update(['category_id' => $defaultCategory->id]);
            }
            return $category->delete();
        });
    }
}