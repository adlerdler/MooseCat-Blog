<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('children')->whereNull('parent_id')->orderBy('sort_order')->get();
        return view('categories.index', compact('categories'));
    }

    public function show(Category $category): View
    {
        $category->load(['children', 'posts']);
        return view('categories.show', compact('category'));
    }

    public function tag(Tag $tag): View
    {
        $tag->load(['posts', 'projects', 'resources']);
        return view('tags.show', compact('tag'));
    }
}
