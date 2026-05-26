<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Front Menu Controller
 * 
 * Handles front-end menu management operations.
 * Provides CRUD functionality for navigation menus.
 */
class FrontMenuController extends Controller
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
     * Display the menu list
     * 
     * @return Response
     */
    public function index(): Response
    {
        $menus = $this->mockDataService->getMenus();
        
        return Inertia::render('admin/FrontMenu', [
            'menus' => $menus,
        ]);
    }

    /**
     * Store a newly created menu item
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'url' => 'required|string',
            'parent_id' => 'nullable|integer',
        ]);

        return back()->with('success', '菜单已创建');
    }

    /**
     * Update the specified menu item
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        return back()->with('success', '菜单已更新');
    }

    /**
     * Remove the specified menu item
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        return back()->with('success', '菜单已删除');
    }
}