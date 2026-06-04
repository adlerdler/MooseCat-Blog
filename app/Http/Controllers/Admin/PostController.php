<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function __construct(protected PostService $postService)
    {
        $this->middleware('permission:manage_posts');
    }

    public function index(): Response
    {
        $user = request()->user();
        $filters = request()->only('category', 'tag', 'status');

        // 如果角色为"作者"，仅显示自己的文章
        if ($user && $user->hasRole('Author')) {
            $filters['author_id'] = $user->id;
        }

        $postsData = $this->postService->getPaginatedPosts(100, $filters);
        $posts = collect($postsData->items())->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'cover_image' => $post->cover_image,
                'status' => $post->status,
                'category_id' => $post->category_id,
                'author_id' => $post->author_id,
                'published_at' => $post->published_at,
                'tags' => $post->tags->pluck('name')->toArray(),
                'meta_title' => $post->meta_title,
                'meta_description' => $post->meta_description,
                'meta_keywords' => $post->meta_keywords,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ];
        })->toArray();
        $categories = Category::orderBy('sort_order')->get();
        $users = User::whereHas('roles')->get();

        return Inertia::render('admin/Posts', [
            'posts' => $posts,
            'categories' => $categories,
            'users' => $users,
            'total' => $postsData->total(),
        ]);
    }

    public function create(): Response
    {
        $categories = Category::orderBy('sort_order')->get(['id', 'name']);
        $tags = Tag::orderBy('name')->get(['id', 'name']);
        $users = User::whereHas('roles')->get(['id', 'name']);

        return Inertia::render('admin/Posts', [
            'categories' => $categories,
            'tags' => $tags,
            'users' => $users,
        ]);
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = $request->user()->id;
        
        $this->postService->createPost($data);

        return redirect()->route('posts.index')->with('success', '文章已创建');
    }

    public function show(Post $post): Response
    {
        $post->load(['author', 'category', 'tags']);

        return Inertia::render('admin/Posts', [
            'post' => $post,
        ]);
    }

    public function edit(Post $post): Response
    {
        $post->load(['tags', 'author']);
        $categories = Category::orderBy('sort_order')->get(['id', 'name']);
        $tags = Tag::orderBy('name')->get(['id', 'name']);
        $users = User::whereHas('roles')->get(['id', 'name']);

        $postData = [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
            'thumbnail' => $post->cover_image,
            'author' => $post->author ? ($post->author->pen_name ?? $post->author->name) : '',
            'category' => $post->category_id,
            'date' => $post->published_at?->format('Y-m-d\TH:i'),
            'tags' => $post->tags->pluck('name')->join(', '),
            'is_featured' => $post->is_featured,
            // SEO
            'meta_title' => $post->meta_title,
            'meta_description' => $post->meta_description,
            'meta_keywords' => $post->meta_keywords,
        ];

        return Inertia::render('admin/Posts', [
            'post' => $postData,
            'categories' => $categories,
            'tags' => $tags,
            'users' => $users,
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->postService->updatePost($post, $request->validated());

        return redirect()->route('posts.index')->with('success', '文章已更新');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index')->with('success', '文章已删除');
    }

    public function publish(Post $post)
    {
        $this->postService->publishPost($post);

        return redirect()->route('posts.index')->with('success', '文章已发布');
    }
}
