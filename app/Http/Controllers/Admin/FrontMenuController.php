<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FrontMenuController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $menus = $this->mockDataService->getMenus();
        
        return Inertia::render('admin/FrontMenu', [
            'menus' => $menus,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'url' => 'required|string',
            'parent_id' => 'nullable|integer',
        ]);

        return back()->with('success', '菜单已创建');
    }

    public function update(Request $request, string $id)
    {
        return back()->with('success', '菜单已更新');
    }

    public function destroy(string $id)
    {
        return back()->with('success', '菜单已删除');
    }
}