<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function getCategories(): Collection
    {
        return Category::query()
            ->withCount('posts')
            ->orderBy('order', 'asc')
            ->get();
    }

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

    public function getCategoryBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    public function createCategory(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
            return Category::create($data);
        });
    }

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
