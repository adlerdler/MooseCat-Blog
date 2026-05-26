<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * User Levels Controller
 * 
 * Handles user level management operations.
 * Provides CRUD functionality for user levels.
 */
class UserLevelsController extends Controller
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
     * Display the user level list
     * 
     * @return Response
     */
    public function index(): Response
    {
        $levels = $this->mockDataService->getUserLevels();
        
        return Inertia::render('admin/UserLevels', [
            'levels' => $levels,
        ]);
    }

    /**
     * Store a newly created user level
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'min_points' => 'required|integer',
            'max_points' => 'required|integer',
        ]);

        return back()->with('success', '用户等级已创建');
    }

    /**
     * Update the specified user level
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        return back()->with('success', '用户等级已更新');
    }

    /**
     * Remove the specified user level
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        return back()->with('success', '用户等级已删除');
    }
}