<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\Project;
use App\Models\Video;
use Illuminate\Support\Facades\Cache;

/**
 * CacheService - 缓存服务
 *
 * 提供统一的缓存管理功能，根据设置自动启用/禁用缓存
 */
class CacheService
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * 统一缓存入口
     *
     * @param string $key 缓存键
     * @param callable $callback 回调函数（返回要缓存的数据）
     * @param string $prefix 缓存前缀（用于标识缓存类型）
     * @return mixed
     */
    public function remember(string $key, callable $callback, string $prefix = ''): mixed
    {
        $cacheEnabled = (bool) $this->settingService->get('cache', false);

        if (!$cacheEnabled) {
            return $callback();
        }

        $duration = (int) $this->settingService->get('cache_duration', 3600);

        return Cache::remember($key, $duration, $callback);
    }

    /**
     * 清除指定缓存
     */
    public function forget(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * 清除文章相关缓存
     */
    public function clearPostCache(?Post $post = null): void
    {
        $this->forget('home_posts_random');
        $this->forget('blog_posts_page_1');
        $this->forget('categories_list');
        $this->forget('authors_list');

        if ($post) {
            $this->forget("blog_post_{$post->slug}");
        }
    }

    /**
     * 清除视频相关缓存
     */
    public function clearVideoCache(?Video $video = null): void
    {
        $this->forget('home_videos_latest');
        $this->forget('videos_list');
        $this->forget('categories_list');

        if ($video) {
            $this->forget("videos_video_{$video->slug}");
        }
    }

    /**
     * 清除项目相关缓存
     */
    public function clearProjectCache(?Project $project = null): void
    {
        $this->forget('home_projects');
        $this->forget('projects_list');
        $this->forget('categories_list');

        if ($project) {
            $this->forget("projects_project_{$project->slug}");
        }
    }

    /**
     * 清除分类相关缓存
     */
    public function clearCategoryCache(?Category $category = null): void
    {
        $this->forget('categories_list');
        $this->forget('home_posts_random');
        $this->forget('blog_posts_page_1');
        $this->forget('home_videos_latest');
        $this->forget('videos_list');
        $this->forget('home_projects');
        $this->forget('projects_list');
    }

    /**
     * 清除菜单缓存
     */
    public function clearMenuCache(): void
    {
        $this->forget('menus_data');
    }

    /**
     * 清除 Footer 缓存
     */
    public function clearFooterCache(): void
    {
        $this->forget('footer_config');
    }

    /**
     * 清除主题缓存
     */
    public function clearThemeCache(): void
    {
        $this->forget('themes_list');
    }

    /**
     * 清除所有静态配置缓存
     */
    public function clearStaticConfigCache(): void
    {
        $this->clearMenuCache();
        $this->clearFooterCache();
        $this->clearThemeCache();
        $this->settingService->refreshCache();
    }
}
