<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuthorProfile;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Interaction;
use App\Models\Post;
use App\Models\Project;
use App\Models\Resource;
use App\Models\User;
use App\Models\Video;
use App\Services\CacheService;
use App\Services\MockDataService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FrontendController extends Controller
{
    protected $mockDataService;
    protected $settingService;
    protected $cacheService;

    public function __construct(MockDataService $mockDataService, SettingService $settingService, CacheService $cacheService)
    {
        $this->mockDataService = $mockDataService;
        $this->settingService = $settingService;
        $this->cacheService = $cacheService;
    }

    public function home(): Response
    {
        $posts = $this->cacheService->remember('home_posts_random', function () {
            $count = Post::where('status', 'published')->count();
            if ($count === 0) {
                return collect();
            }

            // 获取所有已发布文章 ID，随机选取 3 个
            $allIds = Post::where('status', 'published')->pluck('id')->toArray();
            $randomIds = collect($allIds)->random(min(3, count($allIds)))->toArray();

            return Post::with(['author', 'category', 'tags'])
                ->whereIn('id', $randomIds)
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'slug' => $p->slug,
                    'title' => $p->title,
                    'excerpt' => $p->excerpt,
                    'color' => $p->color,
                    'published_at' => $p->published_at?->toISOString(),
                    'category_id' => $p->category_id,
                    'author_id' => $p->author_id,
                    'tags' => $p->tags->pluck('name')->toArray(),
                ]);
        });

        $projects = $this->cacheService->remember('home_projects', function () {
            return Project::query()
                ->where('status', 'completed')
                ->orderBy('sort_order', 'asc')
                ->orderBy('year', 'desc')
                ->limit(3)
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'slug' => $p->slug,
                    'title' => $p->title,
                    'description' => $p->description,
                    'image' => $p->image,
                    'url' => $p->url,
                    'github_url' => $p->github_url,
                    'technologies' => $p->technologies ?? [],
                    'status' => $p->status,
                    'year' => $p->year,
                ]);
        });

        $videos = $this->cacheService->remember('home_videos_latest', function () {
            return Video::with('category')
                ->where('status', 'published')
                ->latest('published_at')
                ->limit(3)
                ->get()
                ->map(fn($v) => [
                    'id' => $v->id,
                    'slug' => $v->slug,
                    'title' => $v->title,
                    'description' => $v->description,
                    'video_id' => $v->video_id,
                    'platform' => $v->platform,
                    'thumbnail' => $v->thumbnail,
                    'duration' => $v->duration,
                    'views_count' => $v->views_count,
                    'likes_count' => $v->likes_count,
                    'published_at' => $v->published_at?->toISOString(),
                ]);
        });
        return Inertia::render('front/Home', [
            'posts' => $posts,
            'projects' => $projects,
            'videos' => $videos,
        ]);
    }

    public function blog(): Response
    {
        $paginator = $this->cacheService->remember('blog_posts_page_1', function () {
            return Post::with(['author', 'category', 'tags'])
                ->where('status', 'published')
                ->latest('published_at')
                ->paginate(14);
        });

        $paginator->getCollection()->transform(fn($p) => [
            'id' => $p->id,
            'slug' => $p->slug,
            'title' => $p->title,
            'excerpt' => $p->excerpt,
            'color' => $p->color,
            'published_at' => $p->published_at?->toISOString(),
            'category_id' => $p->category_id,
            'author_id' => $p->author_id,
            'tags' => $p->tags->pluck('name')->toArray(),
        ]);

        $categories = $this->cacheService->remember('categories_list', function () {
            return Category::where('status', 'active')->get()->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
            ])->toArray();
        });

        $authors = $this->cacheService->remember('authors_list', function () {
            return User::with('authorProfile')->get()->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'penName' => $u->authorProfile?->display_name ?? $u->name,
                'slug' => $u->authorProfile?->slug ?? null,
            ])->toArray();
        });

        return Inertia::render('front/Blog', [
            'posts' => $paginator,
            'categories' => $categories,
            'authors' => $authors,
        ]);
    }

    public function projects(): Response
    {
        $projects = $this->cacheService->remember('projects_list', function () {
            return Project::query()
                ->orderBy('sort_order', 'asc')
                ->orderBy('year', 'desc')
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'slug' => $p->slug,
                    'title' => $p->title,
                    'description' => $p->description,
                    'long_description' => $p->long_description,
                    'image' => $p->image,
                    'url' => $p->url,
                    'github_url' => $p->github_url,
                    'technologies' => $p->technologies ?? [],
                    'status' => $p->status,
                    'year' => $p->year,
                    'client' => $p->client,
                    'role' => $p->role,
                    'views_count' => $p->views_count,
                ]);
        });

        return Inertia::render('front/Projects', [
            'projects' => $projects,
        ]);
    }

    public function resources(): Response
    {
        $resources = Resource::with('category')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'description' => $r->description,
                'format' => $r->format,
                'file_size' => $r->file_size,
                'image' => $r->image,
                'direct_link' => $r->direct_link,
                'drives' => $r->drives ?? [],
                'category_id' => $r->category_id,
                'category_name' => $r->category?->name,
                'downloads_count' => $r->downloads_count,
            ]);

        $categories = Category::where('status', 'active')->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->name]);

        return Inertia::render('front/Resources', [
            'resources' => $resources,
            'categories' => $categories,
        ]);
    }

    public function videos(): Response
    {
        $videos = $this->cacheService->remember('videos_list', function () {
            return Video::with('category')
                ->where('status', 'published')
                ->latest('published_at')
                ->get()
                ->map(fn($v) => [
                    'id' => $v->id,
                    'slug' => $v->slug,
                    'title' => $v->title,
                    'description' => $v->description,
                    'video_id' => $v->video_id,
                    'platform' => $v->platform,
                    'thumbnail' => $v->thumbnail ?? $v->cover_image,
                    'duration' => $v->duration,
                    'views_count' => $v->views_count,
                    'likes_count' => $v->likes_count,
                    'category_id' => $v->category_id,
                    'created_at' => $v->created_at?->format('Y-m-d'),
                ]);
        });

        $categories = $this->cacheService->remember('categories_list', function () {
            return Category::where('status', 'active')->get()->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
            ])->toArray();
        });
        
        return Inertia::render('front/Videos', [
            'videos' => $videos,
            'categories' => $categories,
        ]);
    }

    public function postDetail(Post $post, Request $request): Response
    {
        $post->load(['author', 'category', 'tags']);

        $postData = [
            'id' => $post->id,
            'slug' => $post->slug,
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
            'color' => $post->color,
            'cover_image' => $post->cover_image,
            'published_at' => $post->published_at?->toISOString(),
            'category_id' => $post->category_id,
            'author_id' => $post->author_id,
            'tags' => $post->tags->pluck('name')->toArray(),
            'likes_count' => $post->likes_count,
            'views_count' => $post->views_count,
            'meta_title' => $post->meta_title,
            'meta_description' => $post->meta_description,
            'meta_keywords' => $post->meta_keywords,
        ];

        $categories = Category::where('status', 'active')->get()->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
            ])->toArray();

        $authors = User::with('authorProfile')->get()->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'penName' => $u->authorProfile?->display_name ?? $u->name,
            'slug' => $u->authorProfile?->slug ?? null,
        ])->toArray();

        $comments = Comment::where('post_id', $post->id)
            ->where('is_approved', true)
            ->whereNull('parent_id')  // 只取顶层评论
            ->with(['children' => fn($q) => $q->where('is_approved', true)->orderBy('created_at')])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'post_id' => $c->post_id,
                'parent_id' => $c->parent_id,
                'user_id' => $c->user_id,
                'name' => $c->name,
                'body' => $c->body,
                'is_approved' => $c->is_approved,
                'is_admin' => $c->is_admin,
                'created_at' => $c->created_at?->toISOString(),
                'replies' => $c->children->map(fn($r) => [
                    'id' => $r->id,
                    'parent_id' => $r->parent_id,
                    'name' => $r->name,
                    'body' => $r->body,
                    'is_admin' => $r->is_admin,
                    'is_approved' => $r->is_approved,
                    'created_at' => $r->created_at?->toISOString(),
                ])->values()->toArray(),
            ])->toArray();

        $interactions = Interaction::where('interactable_type', 'App\\Models\\Post')
            ->where('interactable_id', $post->id)
            ->get()
            ->map(fn($i) => [
                'id' => $i->id,
                'user_id' => $i->user_id,
                'interactable_id' => $i->interactable_id,
                'interactable_type' => 'Post',
                'type' => $i->type,
                'created_at' => $i->created_at?->toISOString(),
                'updated_at' => $i->updated_at?->toISOString(),
            ])->toArray();

        return Inertia::render('front/PostDetail', [
            'post' => $postData,
            'categories' => $categories,
            'authors' => $authors,
            'comments' => $comments,
            'interactions' => $interactions,
        ]);
    }

    public function projectDetail(Project $project, Request $request): Response
    {
        $projectData = [
            'id' => $project->id,
            'slug' => $project->slug,
            'title' => $project->title,
            'description' => $project->description,
            'long_description' => $project->long_description,
            'image' => $project->image,
            'url' => $project->url,
            'github_url' => $project->github_url,
            'technologies' => $project->technologies ?? [],
            'status' => $project->status,
            'year' => $project->year,
            'client' => $project->client,
            'role' => $project->role,
            'views_count' => $project->views_count,
            'likes_count' => $project->likes_count,
            'meta_title' => $project->meta_title,
            'meta_description' => $project->meta_description,
            'meta_keywords' => $project->meta_keywords,
        ];

        return Inertia::render('front/ProjectDetail', [
            'project' => $projectData,
        ]);
    }

    public function videoDetail(Video $video, Request $request): Response
    {
        $video->load('category');

        $videoData = [
            'id' => $video->id,
            'slug' => $video->slug,
            'title' => $video->title,
            'description' => $video->description,
            'video_id' => $video->video_id,
            'video_url' => $video->video_url,
            'platform' => $video->platform,
            'thumbnail' => $video->thumbnail ?? $video->cover_image,
            'duration' => $video->duration,
            'views_count' => $video->views_count,
            'likes_count' => $video->likes_count,
            'published_at' => $video->published_at?->toISOString(),
            'meta_title' => $video->meta_title,
            'meta_description' => $video->meta_description,
            'meta_keywords' => $video->meta_keywords,
        ];

        return Inertia::render('front/VideoDetail', [
            'video' => $videoData,
        ]);
    }

    /**
     * 记录资源下载（递增 downloads_count）
     */
    public function trackDownload(Resource $resource): \Illuminate\Http\JsonResponse
    {
        $resource->increment('downloads_count');

        return response()->json([
            'success' => true,
            'downloads_count' => $resource->fresh()->downloads_count,
        ]);
    }

    public function author(string $slug): Response
    {
        // 检查作者简介是否启用
        $siteConfig = $this->settingService->getSiteConfig();
        if (empty($siteConfig['author_bio'])) {
            abort(404);
        }

        $profile = AuthorProfile::with('user')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $displayName = $profile->display_name ?? $profile->user?->name ?? $slug;

        $author = [
            'id' => $profile->id,
            'user_id' => $profile->user_id,
            'slug' => $profile->slug,
            'display_name' => $displayName,
            'bio' => $profile->bio,
            'avatar' => $profile->avatar,
            'role_label' => $profile->role_label,
            'role_title' => $profile->role_title,
            'status_label' => $profile->status_label,
            'status_text' => $profile->status_text,
            'social_links' => $profile->social_links ?? [],
            'skills' => $profile->skills ?? [],
            'manifestos' => $profile->manifestos ?? [],
        ];

        $projects = \App\Models\Project::query()
            ->whereIn('status', ['in-progress', 'planning'])
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'slug' => $p->slug,
                'title' => $p->title,
                'description' => $p->description,
                'image' => $p->image,
                'url' => $p->url,
                'github_url' => $p->github_url,
                'technologies' => $p->technologies ?? [],
                'status' => $p->status,
                'year' => $p->year,
                'sort_order' => $p->sort_order,
            ]);

        return Inertia::render('front/Author', [
            'author' => $author,
            'skills' => $author['skills'],
            'manifestos' => $author['manifestos'],
            'socialLinksObj' => (object)($author['social_links']),
            'projects' => $projects,
        ]);
    }
}