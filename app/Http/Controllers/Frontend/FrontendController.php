<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\Project;
use App\Models\Resource;
use App\Models\Category;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

class FrontendController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
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
        $posts = $this->mockDataService->getPosts(3);
        $projects = Project::query()
            ->where('status', 'completed')
            ->orderBy('sort_order', 'asc')
            ->orderBy('year', 'desc')
            ->limit(3)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'description' => $p->description,
                'image' => $p->image,
                'url' => $p->url,
                'github_url' => $p->github_url,
                'technologies' => $p->technologies ?? [],
                'status' => $p->status,
                'year' => $p->year,
            ]);
        $videos = $this->mockDataService->getVideos(3);
        $menu = $this->mockDataService->getMenu();
        $siteConfig = $this->mockDataService->getSiteConfig();
        $footerConfig = $this->getFooterConfig();
        $themes = $this->mockDataService->getThemes();

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

    private function getConfigData(): array
    {
        return [
            'menus' => $this->mockDataService->getMenu(),
            'siteConfig' => $this->mockDataService->getSiteConfig(),
            'footerConfig' => $this->getFooterConfig(),
            'themes' => $this->mockDataService->getThemes(),
        ];
    }

    public function blog(): Response
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
                'category_name' => $r->category?->name,
                'downloads_count' => $r->downloads_count,
            ]);

        $categories = Category::all()->map(fn($c) => ['id' => $c->id, 'name' => $c->name]);

        return Inertia::render('front/Resources', [
            'resources' => $resources,
            'categories' => $categories,
            ...$this->getConfigData(),
        ]);
    }

    public function videos(): Response
    {
        $videos = $this->mockDataService->getVideos();

        return Inertia::render('front/Videos', [
            'videos' => $videos,
            ...$this->getConfigData(),
        ]);
    }

    public function postDetail($id): Response
    {
        $posts = $this->mockDataService->getPosts();
        $post = collect($posts)->firstWhere('id', $id);
        
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
            ...$this->getConfigData(),
        ]);
    }

    public function projectDetail($id): Response
    {
        $project = Project::findOrFail($id);
        $project->increment('views_count');

        $projectData = [
            'id' => $project->id,
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
        ];

        return Inertia::render('front/ProjectDetail', [
            'project' => $projectData,
            ...$this->getConfigData(),
        ]);
    }

    public function videoDetail($id): Response
    {
        $videos = $this->mockDataService->getVideos();
        $video = collect($videos)->firstWhere('id', $id);

        return Inertia::render('front/VideoDetail', [
            'video' => $video,
            ...$this->getConfigData(),
        ]);
    }

    public function author(): Response
    {
        $authorProfiles = $this->mockDataService->getAuthorProfiles();
        $author = collect($authorProfiles)->firstWhere('user_id', 9);
        
        $skills = $author ? ($author['skills'] ?? []) : [];
        $manifestos = $author ? ($author['manifestos'] ?? []) : [];
        $socialLinks = $author ? ($author['social_links'] ?? []) : [];
        
        $projects = $this->mockDataService->getProjects();
        $activeProjects = collect($projects)->filter(function ($project) {
            return in_array($project['status'] ?? '', ['in-progress', 'planning']);
        })->values();

        return Inertia::render('front/Author', [
            'author' => $author,
            'skills' => $skills,
            'manifestos' => $manifestos,
            'socialLinksObj' => (object)$socialLinks,
            'projects' => $activeProjects,
            ...$this->getConfigData(),
        ]);
    }
}