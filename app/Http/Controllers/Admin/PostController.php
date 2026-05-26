<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
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
        $posts = $this->mockDataService->getPosts();
        $categories = $this->mockDataService->getCategories();
        $users = $this->mockDataService->getUsers();
        
        return Inertia::render('admin/Posts', [
            'posts' => $posts,
            'categories' => $categories,
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $posts = $this->mockDataService->getPosts();
        $categories = $this->mockDataService->getCategories();
        $tags = $this->mockDataService->getTags();
        $users = $this->mockDataService->getUsers();
        
        return Inertia::render('admin/Posts', [
            'posts' => $posts,
            'categories' => $categories,
            'tags' => $tags,
            'users' => $users,
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
        $posts = $this->mockDataService->getPosts();
        $post = collect($posts)->firstWhere('id', $id);
        $categories = $this->mockDataService->getCategories();
        $tags = $this->mockDataService->getTags();
        $users = $this->mockDataService->getUsers();
        
        return Inertia::render('admin/Posts', [
            'posts' => $posts,
            'post' => $post,
            'categories' => $categories,
            'tags' => $tags,
            'users' => $users,
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
