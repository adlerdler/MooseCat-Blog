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
            'name' => 'required|string',
            'min_points' => 'required|integer',
            'max_points' => 'required|integer',
        ]);

        return back()->with('success', '用户等级已创建');
    }

    public function update(Request $request, string $id)
    {
        return back()->with('success', '用户等级已更新');
    }

    public function destroy(string $id)
    {
        return back()->with('success', '用户等级已删除');
    }
}