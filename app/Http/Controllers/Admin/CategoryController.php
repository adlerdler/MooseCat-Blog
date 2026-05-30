<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Category Controller
 * 
 * Handles category management operations.
 * Provides CRUD functionality for blog categories.
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     * 
     * @return Response
     */
    public function index(): Response
    {
        $categories = Category::with('parent')
            ->orderBy('sort_order')
            ->get();
        
        return Inertia::render('admin/Categories', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new category.
     * 
     * @return Response
     */
    public function create(): Response
    {
        $parentCategories = Category::orderBy('sort_order')->get(['id', 'name']);
        
        return Inertia::render('admin/Categories', [
            'parentCategories' => $parentCategories,
        ]);
    }

    /**
     * Store a newly created category in storage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer',
        ]);

        Category::create($validated);

        return back()->with('success', '分类已创建');
    }

    /**
     * Display the specified category.
     * 
     * @param string $id
     */
    public function show(string $id)
    {
        $category = Category::with(['parent', 'children', 'posts'])->findOrFail($id);
        
        return Inertia::render('admin/Categories', [
            'category' => $category,
        ]);
    }

    /**
     * Show the form for editing the specified category.
     * 
     * @param string $id
     * @return Response
     */
    public function edit(string $id): Response
    {
        $category = Category::with('parent')->findOrFail($id);
        $parentCategories = Category::where('id', '!=', $id)
            ->orderBy('sort_order')
            ->get(['id', 'name']);
        
        return Inertia::render('admin/Categories', [
            'category' => $category,
            'parentCategories' => $parentCategories,
        ]);
    }

    /**
     * Update the specified category in storage.
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer',
        ]);

        $category->update($validated);

        return back()->with('success', '分类已更新');
    }

    /**
     * Remove the specified category from storage.
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return back()->with('success', '分类已删除');
    }
}
