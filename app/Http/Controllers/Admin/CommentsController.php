<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Comments Controller
 * 
 * Handles comment management operations.
 * Provides functionality for approving, rejecting, and deleting comments.
 */
class CommentsController extends Controller
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
     * Display the comment list
     * 
     * @return Response
     */
    public function index(): Response
    {
        $comments = $this->mockDataService->getComments();
        $posts = $this->mockDataService->getPosts();

        return Inertia::render('admin/Comments', [
            'comments' => $comments,
            'posts' => $posts,
        ]);
    }

    /**
     * Update the comment status
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,approved,rejected',
        ]);

        return back()->with('success', '评论状态已更新');
    }

    /**
     * Remove the specified comment
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        return back()->with('success', '评论已删除');
    }
}