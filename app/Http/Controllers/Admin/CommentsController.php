<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommentsController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $comments = $this->mockDataService->getComments();
        
        return Inertia::render('admin/Comments', [
            'comments' => $comments,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,approved,rejected',
        ]);

        return back()->with('success', '评论状态已更新');
    }

    public function destroy(string $id)
    {
        return back()->with('success', '评论已删除');
    }
}