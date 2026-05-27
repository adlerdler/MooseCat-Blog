<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

/**
 * CategoryRepository - 分类数据访问层
 * 
 * 封装分类相关的复杂查询逻辑，包括带文章数的分类列表、树形结构等功能。
 * Encapsulates complex query logic related to categories, including categories with 
 * post counts, tree structure and other functionalities.
 */
class CategoryRepository
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
     * 获取所有分类（带文章数量）
     * Get all categories with post counts
     */
    public function getCategoriesWithCount(): Collection
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
            ->withCount('posts')
            ->orderBy('order', 'asc')
            ->get();
    }

    /**
     * 获取平铺的分类树（带层级缩进）
     * Get flat category tree with level indentation
     */
    public function getFlatCategoryTree(): Collection
    {
        $tree = $this->getCategoryTree();
        return $this->flattenTree($tree);
    }

    /**
     * 递归平铺树形结构
     * Recursively flatten tree structure
     */
    private function flattenTree(Collection $categories, int $parentId = null, int $depth = 0): Collection
    {
        $result = collect();

        foreach ($categories as $category) {
            $category->depth = $depth;
            $result->push($category);

            if ($category->children && $category->children->isNotEmpty()) {
                $result = $result->merge(
                    $this->flattenTree($category->children, $category->id, $depth + 1)
                );
            }
        }

        return $result;
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
     * 获取有文章的分类
     * Get categories that have posts
     */
    public function getCategoriesWithPosts(): Collection
    {
        return Category::query()
            ->withCount('posts')
            ->having('posts_count', '>', 0)
            ->orderBy('order', 'asc')
            ->get();
    }

    /**
     * 获取分类及其后代分类的ID
     * Get category and its descendants IDs
     */
    public function getCategoryAndDescendantsIds(int $categoryId): array
    {
        $ids = [$categoryId];
        $children = Category::where('parent_id', $categoryId)->pluck('id');

        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getCategoryAndDescendantsIds($childId));
        }

        return $ids;
    }
}