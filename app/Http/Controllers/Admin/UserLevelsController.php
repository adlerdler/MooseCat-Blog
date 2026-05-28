<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserLevelsController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $levels = $this->mockDataService->getUserLevels();
        
        return Inertia::render('admin/UserLevels', [
            'levels' => $levels,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'level' => 'required|integer',
            'min_points' => 'required|integer|min:0',
            'max_points' => 'nullable|integer|gte:min_points',
            'color' => 'required|string|max:50',
        ]);

        return back()->with('success', '用户等级已创建');
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'level' => 'required|integer',
            'min_points' => 'required|integer|min:0',
            'max_points' => 'nullable|integer|gte:min_points',
            'color' => 'required|string|max:50',
        ]);

        return back()->with('success', '用户等级已更新');
    }

    public function destroy(string $id)
    {
        return back()->with('success', '用户等级已删除');
    }
}