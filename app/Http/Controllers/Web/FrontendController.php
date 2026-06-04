<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuthorProfile;
use App\Models\Category;
use App\Models\Comment;
use App\Models\FooterLink;
use App\Models\Interaction;
use App\Models\Menu;
use App\Models\Post;
use App\Models\Project;
use App\Models\Resource;
use App\Models\User;
use App\Models\Video;
use App\Services\MockDataService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FrontendController extends Controller
{
    protected $mockDataService;
    protected $settingService;

    public function __construct(MockDataService $mockDataService, SettingService $settingService)
    {
        $this->mockDataService = $mockDataService;
        $this->settingService = $settingService;
    }

    private function getFooterConfig(): array
    {
        $socialLinks = FooterLink::socialLinks()
            ->active()
            ->get()
            ->map(function ($link) {
                return [
                    'id' => $link->id,
                    'platform' => $link->platform,
                    'url' => $link->url,
                    'icon_name' => $link->icon_name ?? $link->icon,
                    'label' => $link->label,
                    'sort_order' => $link->sort_order,
                    'is_active' => $link->is_active,
                ];
            });

        $categoryLinks = FooterLink::categoryLinks()
            ->active()
            ->get()
            ->map(function ($link) {
                return [
                    'id' => $link->id,
                    'label' => $link->label,
                    'url' => $link->url,
                    'sort_order' => $link->sort_order,
                    'is_active' => $link->is_active,
                ];
            });

        $dataLinks = FooterLink::dataLinks()
            ->active()
            ->get()
            ->map(function ($link) {
                return [
                    'id' => $link->id,
                    'label' => $link->label,
                    'url' => $link->url,
                    'sort_order' => $link->sort_order,
                    'is_active' => $link->is_active,
                ];
            });

        return [
            'social_links' => $socialLinks,
            'nav_links' => [
                'categories' => $categoryLinks,
                'data' => $dataLinks,
            ],
        ];
    }

    public function home(): Response
    {
        $posts = Post::with(['author', 'category', 'tags'])
            ->where('status', 'published')
            ->inRandomOrder()
            ->limit(3)
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

        $projects = Project::query()
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

        $videos = Video::with('category')
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
        $menu = $this->getMenuData();
        $siteConfig = $this->settingService->getSiteConfig();
        $footerConfig = $this->getFooterConfig();
        $themes = \App\Models\Theme::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'label' => $t->label,
                'color' => $t->color,
                'sort_order' => $t->sort_order,
                'is_active' => $t->is_active,
                'is_default' => $t->is_default,
                'preview_image' => $t->preview_image,
            ]);

        return Inertia::render('front/Home', [
            'posts' => $posts,
            'projects' => $projects,
            'videos' => $videos,
            'menus' => $menu,
            'siteConfig' => $siteConfig,
            'footerConfig' => $footerConfig,
            'themes' => $themes,
        ]);
    }

    /**
     * 获取扁平菜单数据（匹配前端 useMenuItems.js 的格式要求）
     * 前端通过 type 字段过滤 front/admin，通过 parent_id 自建树
     */
    private function getMenuData(): array
    {
        return Menu::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($menu) => [
                'id' => $menu->id,
                'type' => $menu->type,
                'parent_id' => $menu->parent_id,
                'label_key' => $menu->label_key,
                'icon_name' => $menu->icon_name,
                'path' => $menu->path,
                'component_name' => $menu->component_name,
                'sort_order' => $menu->sort_order,
                'is_active' => $menu->is_active,
            ])
            ->toArray();
    }

    private function getConfigData(): array
    {
        $themes = \App\Models\Theme::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'label' => $t->label,
                'color' => $t->color,
                'sort_order' => $t->sort_order,
                'is_active' => $t->is_active,
                'is_default' => $t->is_default,
                'preview_image' => $t->preview_image,
            ]);

        return [
            'menus' => $this->getMenuData(),
            'siteConfig' => $this->settingService->getSiteConfig(),
            'footerConfig' => $this->getFooterConfig(),
            'themes' => $themes,
        ];
    }

    public function blog(): Response
    {
        $paginator = Post::with(['author', 'category', 'tags'])
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(14);

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

        return Inertia::render('front/Blog', [
            'posts' => $paginator,
            'categories' => $categories,
            'authors' => $authors,
            ...$this->getConfigData(),
        ]);
    }

    public function projects(): Response
    {
        $projects = Project::query()
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

        return Inertia::render('front/Projects', [
            'projects' => $projects,
            ...$this->getConfigData(),
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
            ...$this->getConfigData(),
        ]);
    }

    public function videos(): Response
    {
        $videos = Video::with('category')
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

        $categories = Category::where('status', 'active')->get()->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
        ])->toArray();
        
        return Inertia::render('front/Videos', [
            'videos' => $videos,
            'categories' => $categories,
            ...$this->getConfigData(),
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
            ...$this->getConfigData(),
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
            ...$this->getConfigData(),
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
            ...$this->getConfigData(),
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
            ...$this->getConfigData(),
        ]);
    }
}