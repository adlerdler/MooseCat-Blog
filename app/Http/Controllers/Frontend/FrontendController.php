<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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

    public function home(): Response
    {
        $posts = $this->mockDataService->getPosts(3);
        $projects = $this->mockDataService->getProjects(3);
        $videos = $this->mockDataService->getVideos(3);
        $menu = $this->mockDataService->getMenu();
        $siteConfig = $this->mockDataService->getSiteConfig();
        $footerConfig = $this->mockDataService->getFooterConfig();
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
            'footerConfig' => $this->mockDataService->getFooterConfig(),
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
        $projects = $this->mockDataService->getProjects();

        return Inertia::render('front/Projects', [
            'projects' => $projects,
            ...$this->getConfigData(),
        ]);
    }

    public function resources(): Response
    {
        $resources = $this->mockDataService->getResources();
        $categories = $this->mockDataService->getCategories();
        
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
        $projects = $this->mockDataService->getProjects();
        $project = collect($projects)->firstWhere('id', $id);

        return Inertia::render('front/ProjectDetail', [
            'project' => $project,
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