<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $posts = $this->mockDataService->getPosts();
        $categories = $this->mockDataService->getCategories();
        $authors = $this->mockDataService->getAuthors();

        $perPage = 14;
        $total = count($posts);
        $lastPage = (int)ceil($total / $perPage);

        return Inertia::render('front/Blog', [
            'posts' => (object)[
                'data' => array_slice($posts, 0, $perPage),
                'current_page' => 1,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
            ],
            'categories' => $categories,
            'authors' => $authors,
        ]);
    }

    public function show($slug): Response
    {
        $posts = $this->mockDataService->getPosts();
        $post = $this->mockDataService->getPostBySlug($slug) 
            ?? collect($posts)->firstWhere('id', $slug);
        
        $categories = $this->mockDataService->getCategories();
        $authors = $this->mockDataService->getAuthors();
        $comments = $this->mockDataService->getComments();
        $interactions = $this->mockDataService->getInteractions();

        return Inertia::render('front/PostDetail', [
            'post' => $post,
            'categories' => $categories,
            'authors' => $authors,
            'comments' => $comments,
            'interactions' => $interactions,
        ]);
    }
}