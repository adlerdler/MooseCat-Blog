<?php

namespace App\Services;

/**
 * MockDataService - 模拟数据服务类
 * 
 * 提供前端开发所需的模拟数据，从JSON文件加载数据并支持各种数据查询操作。
 * Provides mock data for frontend development, loading data from JSON files and 
 * supporting various data query operations.
 */
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
    private array $menu = [];
    private array $pageSeo = [];

    private string $dataPath;

    /**
     * 构造函数 - 初始化数据路径并加载所有数据
     * Constructor - Initialize data path and load all data
     */
    public function __construct()
    {
        $this->dataPath = resource_path('js/data');
        $this->loadAllData();
    }

    /**
     * 加载JSON文件
     * Load JSON file
     */
    private function loadJson(string $filename): array
    {
        $path = $this->dataPath . '/' . $filename;
        if (!file_exists($path)) {
            return [];
        }
        $content = file_get_contents($path);
        return json_decode($content, true) ?? [];
    }

    /**
     * 加载所有数据
     * Load all data
     */
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

    /**
     * 根据ID查找分类
     * Find category by ID
     */
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

    /**
     * 根据ID查找作者
     * Find author by ID
     */
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

    /**
     * 根据ID数组查找标签
     * Find tags by IDs
     */
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

    /**
     * 获取文章列表
     * Get posts
     */
    public function getPosts(int $limit = null): array
    {
        if ($limit !== null) {
            return array_slice($this->posts, 0, $limit);
        }
        return $this->posts;
    }

    /**
     * 获取项目列表
     * Get projects
     */
    public function getProjects(int $limit = null): array
    {
        if ($limit !== null) {
            return array_slice($this->projects, 0, $limit);
        }
        return $this->projects;
    }

    /**
     * 获取视频列表
     * Get videos
     */
    public function getVideos(int $limit = null): array
    {
        if ($limit !== null) {
            return array_slice($this->videos, 0, $limit);
        }
        return $this->videos;
    }

    /**
     * 获取分类列表
     * Get categories
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * 获取标签列表
     * Get tags
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * 获取标签关联列表
     * Get taggables
     */
    public function getTagsables(): array
    {
        return $this->tagsables;
    }

    /**
     * 获取用户列表
     * Get users
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * 获取角色列表
     * Get roles
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * 获取权限列表
     * Get permissions
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * 获取角色权限关联
     * Get role permissions
     */
    public function getRolePermissions(): array
    {
        return $this->rolePermissions;
    }

    /**
     * 获取日志列表
     * Get journals
     */
    public function getJournals(int $limit = null): array
    {
        if ($limit !== null) {
            return array_slice($this->journals, 0, $limit);
        }
        return $this->journals;
    }

    /**
     * 获取评论列表
     * Get comments
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * 获取通知列表
     * Get notifications
     */
    public function getNotifications(): array
    {
        return $this->notifications;
    }

    /**
     * 获取广告列表
     * Get advertisements
     */
    public function getAdvertisements(): array
    {
        return $this->advertisements;
    }

    /**
     * 获取广告位置列表
     * Get ad positions
     */
    public function getAdPositions(): array
    {
        return $this->adPositions;
    }

    /**
     * 获取资源列表
     * Get resources
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * 获取订阅者列表
     * Get subscribers
     */
    public function getSubscribers(): array
    {
        return $this->subscribers;
    }

    /**
     * 获取互动记录列表
     * Get interactions
     */
    public function getInteractions(): array
    {
        return $this->interactions;
    }

    /**
     * 获取访问记录列表
     * Get visits
     */
    public function getVisits(): array
    {
        return $this->visits;
    }

    /**
     * 获取系统日志列表
     * Get logs
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * 获取备份列表
     * Get backup
     */
    public function getBackup(): array
    {
        return $this->backup;
    }

    /**
     * 获取备份列表（别名）
     * Get backups
     */
    public function getBackups(): array
    {
        return $this->backup;
    }

    /**
     * 获取作者资料列表
     * Get author profiles
     */
    public function getAuthorProfiles(): array
    {
        return $this->authorProfiles;
    }

    /**
     * 获取主题列表
     * Get themes
     */
    public function getThemes(): array
    {
        return $this->themes;
    }

    /**
     * 获取用户等级列表
     * Get user levels
     */
    public function getUserLevels(): array
    {
        return $this->userLevels;
    }

    /**
     * 获取站点配置
     * Get site config
     */
    public function getSiteConfig(): array
    {
        return $this->siteConfig;
    }

    /**
     * 获取SEO配置
     * Get SEO config
     */
    public function getSeoConfig(): array
    {
        return $this->seoConfig;
    }

    /**
     * 获取页脚配置
     * Get footer config
     */
    public function getFooterConfig(): array
    {
        return $this->footerConfig;
    }

    /**
     * 获取邮件配置
     * Get mail config
     */
    public function getMailConfig(): array
    {
        return $this->mailConfig;
    }

    /**
     * 获取邮件模板
     * Get email templates
     */
    public function getEmailTemplates(): array
    {
        return $this->emailTemplates;
    }

    /**
     * 获取菜单数据
     * Get menu
     */
    public function getMenu(): array
    {
        return $this->menu;
    }

    /**
     * 获取菜单列表（别名）
     * Get menus
     */
    public function getMenus(): array
    {
        return $this->menu;
    }

    /**
     * 获取页面SEO配置
     * Get page SEO
     */
    public function getPageSeo(): array
    {
        return $this->pageSeo;
    }

    /**
     * 根据slug获取文章
     * Get post by slug
     */
    public function getPostBySlug(string $slug): ?array
    {
        foreach ($this->posts as $post) {
            if ($post['slug'] === $slug) {
                return $post;
            }
        }
        return null;
    }

    /**
     * 根据分类获取文章
     * Get posts by category
     */
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

    /**
     * 分页获取文章
     * Paginate posts
     */
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

    /**
     * 获取作者列表
     * Get authors
     */
    public function getAuthors(): array
    {
        return $this->authorProfiles;
    }

    /**
     * 根据ID获取用户
     * Get user by ID
     */
    public function getUserById(int $id): ?array
    {
        foreach ($this->users as $user) {
            if ($user['id'] === $id) {
                return $user;
            }
        }
        return null;
    }

    /**
     * 根据ID获取角色
     * Get role by ID
     */
    public function getRoleById(int $id): ?array
    {
        foreach ($this->roles as $role) {
            if ($role['id'] === $id) {
                return $role;
            }
        }
        return null;
    }

    /**
     * 根据ID获取分类
     * Get category by ID
     */
    public function getCategoryById(int $id): ?array
    {
        foreach ($this->categories as $category) {
            if ($category['id'] === $id) {
                return $category;
            }
        }
        return null;
    }

    /**
     * 根据ID获取标签
     * Get tag by ID
     */
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