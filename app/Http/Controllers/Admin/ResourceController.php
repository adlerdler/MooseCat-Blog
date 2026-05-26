<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $resources = $this->mockDataService->getResources();
        $categories = $this->mockDataService->getCategories();
        
        return Inertia::render('admin/Resources', [
            'resources' => $resources,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->mockDataService->getCategories();
        
        return Inertia::render('admin/Resources', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $resources = $this->mockDataService->getResources();
        $resource = collect($resources)->firstWhere('id', $id);
        $categories = $this->mockDataService->getCategories();
        
        return Inertia::render('admin/Resources', [
            'resource' => $resource,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
