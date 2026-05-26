<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
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
    protected $mockDataService;

    /**
     * Constructor
     * 
     * @param MockDataService $mockDataService
     */
    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    /**
     * Display a listing of the categories.
     * 
     * @return Response
     */
    public function index(): Response
    {
        $categories = $this->mockDataService->getCategories();
        
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
        return Inertia::render('admin/Categories');
    }

    /**
     * Store a newly created category in storage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Handle category creation
    }

    /**
     * Display the specified category.
     * 
     * @param string $id
     */
    public function show(string $id)
    {
        // Show category details
    }

    /**
     * Show the form for editing the specified category.
     * 
     * @param string $id
     * @return Response
     */
    public function edit(string $id): Response
    {
        $categories = $this->mockDataService->getCategories();
        $category = collect($categories)->firstWhere('id', $id);
        
        return Inertia::render('admin/Categories', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified category in storage.
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        // Handle category update
    }

    /**
     * Remove the specified category from storage.
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        // Handle category deletion
    }
}
