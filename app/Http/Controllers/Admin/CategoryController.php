<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): Response
    {
        $categories = Category::with(['parent', 'posts'])
            ->orderBy('sort_order')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'parent_id' => $c->parent_id,
                'parent_name' => $c->parent?->name,
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description,
                'status' => $c->status,
                'sort_order' => $c->sort_order,
                'posts_count' => $c->posts_count ?? $c->posts->count(),
                'created_at' => $c->created_at?->format('Y-m-d'),
            ]);

        $parentCategories = Category::orderBy('sort_order')->get(['id', 'name']);

        return Inertia::render('admin/Categories', [
            'categories' => $categories,
            'parentCategories' => $parentCategories,
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->categoryService->createCategory($data);

        return back()->with('success', '分类已创建');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();
        $this->categoryService->updateCategory($category, $data);

        return back()->with('success', '分类已更新');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->deleteCategory($category);

        return back()->with('success', '分类已删除');
    }
}