<?php

namespace App\Services;

class MockDataService
{
    private array $posts = [];
    private array $projects = [];
    private array $videos = [];
    private array $categories = [];
    private array $tags = [];
    private array $users = [];
    private array $roles = [];
    private array $permissions = [];
    private array $rolePermissions = [];
    private array $journals = [];
    private array $comments = [];
    private array $notifications = [];
    private array $advertisements = [];
    private array $adPositions = [];
    private array $media = [];
    private array $resources = [];
    private array $tagsables = [];
    private array $subscribers = [];
    private array $interactions = [];
    private array $visits = [];
    private array $logs = [];
    private array $backup = [];
    private array $authorProfiles = [];
    private array $themes = [];
    private array $userLevels = [];
    private array $siteConfig = [];
    private array $seoConfig = [];
    private array $footerConfig = [];
    private array $mailConfig = [];
    private array $emailTemplates = [];
    private array $i18nConfig = [];
    private array $menu = [];
    private array $pageSeo = [];

    private string $dataPath;

    public function __construct()
    {
        $this->dataPath = resource_path('js/data');
        $this->loadAllData();
    }

    private function loadJson(string $filename): array
    {
        $path = $this->dataPath . '/' . $filename;
        if (!file_exists($path)) {
            return [];
        }
        $content = file_get_contents($path);
        return json_decode($content, true) ?? [];
    }

    private function loadAllData(): void
    {
        $this->videos = $this->loadJson('videos.json');
        $this->projects = $this->loadJson('projects.json');
        $this->categories = $this->loadJson('categories.json');
        $this->tags = $this->loadJson('tags.json');
        $this->users = $this->loadJson('users.json');
        $this->roles = $this->loadJson('roles.json');
        $this->permissions = $this->loadJson('permissions.json');
        $this->rolePermissions = $this->loadJson('role_permissions.json');
        $this->journals = $this->loadJson('journals.json');
        $this->comments = $this->loadJson('comments.json');
        $this->notifications = $this->loadJson('notifications.json');
        $this->advertisements = $this->loadJson('advertisements.json');
        $this->adPositions = $this->loadJson('ad_positions.json');
        $this->media = $this->loadJson('media.json');
        $this->resources = $this->loadJson('resources.json');
        $this->tagsables = $this->loadJson('taggables.json');
        $this->subscribers = $this->loadJson('subscribers.json');
        $this->interactions = $this->loadJson('interactions.json');
        $this->visits = $this->loadJson('visits.json');
        $this->logs = $this->loadJson('logs.json');
        $this->backup = $this->loadJson('backup.json');
        $this->authorProfiles = $this->loadJson('author_profiles.json');
        $this->themes = $this->loadJson('themes.json');
        $this->userLevels = $this->loadJson('user_levels.json');
        $this->siteConfig = $this->loadJson('site_config.json');
        $this->seoConfig = $this->loadJson('seo_config.json');
        $this->footerConfig = $this->loadJson('footer_config.json');
        $this->mailConfig = $this->loadJson('mail_config.json');
        $this->emailTemplates = $this->loadJson('email_templates.json');
        $this->i18nConfig = $this->loadJson('i18n_config.json');
        $this->menu = $this->loadJson('menu.json');
        $this->pageSeo = $this->loadJson('page_seo.json');

        $posts = $this->loadJson('posts.json');
        foreach ($posts as $post) {
            $post['category'] = $this->findCategoryById($post['category_id'] ?? null);
            $post['author'] = $this->findAuthorById($post['author_id'] ?? null);
            $post['tags'] = $this->findTagsByIds($post['tags'] ?? []);
            $this->posts[] = $post;
        }
    }

    private function findCategoryById(?int $id): ?array
    {
        if ($id === null) return null;
        foreach ($this->categories as $cat) {
            if ($cat['id'] === $id) {
                return ['id' => $cat['id'], 'name' => $cat['name'], 'slug' => $cat['slug']];
            }
        }
        return null;
    }

    private function findAuthorById(?int $id): ?array
    {
        if ($id === null) return null;
        $authors = [
            1 => ['id' => 1, 'name' => 'Adler', 'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop'],
            2 => ['id' => 2, 'name' => 'Chen Wei', 'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop'],
            3 => ['id' => 3, 'name' => 'Lin Feng', 'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop'],
        ];
        return $authors[$id] ?? null;
    }

    private function findTagsByIds(array $tagIds): array
    {
        $allTags = [
            1 => ['id' => 1, 'name' => 'Design', 'slug' => 'design'],
            2 => ['id' => 2, 'name' => 'UI/UX', 'slug' => 'ui-ux'],
            3 => ['id' => 3, 'name' => 'Typography', 'slug' => 'typography'],
            4 => ['id' => 4, 'name' => 'Fonts', 'slug' => 'fonts'],
            5 => ['id' => 5, 'name' => 'Technology', 'slug' => 'technology'],
            6 => ['id' => 6, 'name' => 'Philosophy', 'slug' => 'philosophy'],
        ];
        return array_filter(array_map(fn($id) => $allTags[$id]['name'] ?? null, $tagIds));
    }

    public function getPosts(int $limit = null): array
    {
        if ($limit !== null) {
            return array_slice($this->posts, 0, $limit);
        }
        return $this->posts;
    }

    public function getProjects(int $limit = null): array
    {
        if ($limit !== null) {
            return array_slice($this->projects, 0, $limit);
        }
        return $this->projects;
    }

    public function getVideos(int $limit = null): array
    {
        if ($limit !== null) {
            return array_slice($this->videos, 0, $limit);
        }
        return $this->videos;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function getRolePermissions(): array
    {
        return $this->rolePermissions;
    }

    public function getJournals(int $limit = null): array
    {
        if ($limit !== null) {
            return array_slice($this->journals, 0, $limit);
        }
        return $this->journals;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function getNotifications(): array
    {
        return $this->notifications;
    }

    public function getAdvertisements(): array
    {
        return $this->advertisements;
    }

    public function getAdPositions(): array
    {
        return $this->adPositions;
    }

    public function getMedia(): array
    {
        return $this->media;
    }

    public function getResources(): array
    {
        return $this->resources;
    }

    public function getSubscribers(): array
    {
        return $this->subscribers;
    }

    public function getInteractions(): array
    {
        return $this->interactions;
    }

    public function getVisits(): array
    {
        return $this->visits;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function getBackup(): array
    {
        return $this->backup;
    }

    public function getBackups(): array
    {
        return $this->backup;
    }

    public function getAuthorProfiles(): array
    {
        return $this->authorProfiles;
    }

    public function getThemes(): array
    {
        return $this->themes;
    }

    public function getUserLevels(): array
    {
        return $this->userLevels;
    }

    public function getSiteConfig(): array
    {
        return $this->siteConfig;
    }

    public function getSeoConfig(): array
    {
        return $this->seoConfig;
    }

    public function getFooterConfig(): array
    {
        return $this->footerConfig;
    }

    public function getMailConfig(): array
    {
        return $this->mailConfig;
    }

    public function getEmailTemplates(): array
    {
        return $this->emailTemplates;
    }

    public function getI18nConfig(): array
    {
        return $this->i18nConfig;
    }

    public function getMenu(): array
    {
        return $this->menu;
    }

    public function getMenus(): array
    {
        return $this->menu;
    }

    public function getPageSeo(): array
    {
        return $this->pageSeo;
    }

    public function getPostBySlug(string $slug): ?array
    {
        foreach ($this->posts as $post) {
            if ($post['slug'] === $slug) {
                return $post;
            }
        }
        return null;
    }

    public function getPostsByCategory(string $categorySlug, int $limit = null): array
    {
        $filtered = array_filter($this->posts, function ($post) use ($categorySlug) {
            return $post['category']['slug'] ?? '' === $categorySlug;
        });
        $result = array_values($filtered);
        if ($limit !== null) {
            return array_slice($result, 0, $limit);
        }
        return $result;
    }

    public function paginatePosts(int $page = 1, int $perPage = 6): array
    {
        $total = count($this->posts);
        $lastPage = (int)ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        return [
            'data' => array_slice($this->posts, $offset, $perPage),
            'current_page' => $page,
            'last_page' => $lastPage,
            'per_page' => $perPage,
            'total' => $total,
        ];
    }

    public function getAuthors(): array
    {
        return $this->authorProfiles;
    }

    public function getUserById(int $id): ?array
    {
        foreach ($this->users as $user) {
            if ($user['id'] === $id) {
                return $user;
            }
        }
        return null;
    }

    public function getRoleById(int $id): ?array
    {
        foreach ($this->roles as $role) {
            if ($role['id'] === $id) {
                return $role;
            }
        }
        return null;
    }

    public function getCategoryById(int $id): ?array
    {
        foreach ($this->categories as $category) {
            if ($category['id'] === $id) {
                return $category;
            }
        }
        return null;
    }

    public function getTagById(int $id): ?array
    {
        foreach ($this->tags as $tag) {
            if ($tag['id'] === $id) {
                return $tag;
            }
        }
        return null;
    }
}