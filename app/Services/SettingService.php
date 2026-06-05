<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * SettingService - 设置服务类
 * 
 * 提供系统设置的管理功能，支持配置缓存、站点配置、SEO配置和评论配置的读取与更新。
 * Provides system setting management functionality, supporting configuration caching, 
 * site config, SEO config and comment config reading and updating.
 */
class SettingService
{
    private const CACHE_KEY = 'settings_single';

    /**
     * 获取所有设置（带缓存）
     * Get all settings with cache
     */
    public function getAll(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $setting = Setting::first();
            if (!$setting) {
                Setting::create([]);
                $setting = Setting::first();
            }
            return $setting ? $setting->toArray() : [];
        });
    }

    /**
     * 获取单个设置
     * Get single setting
     */
    public function get(string $key, $default = null)
    {
        $settings = $this->getAll();
        return $settings[$key] ?? $default;
    }

    /**
     * 设置单个配置
     * Set single setting
     */
    public function set(string $key, $value): void
    {
        DB::transaction(function () use ($key, $value) {
            $setting = Setting::first();
            if ($setting) {
                $setting->update([$key => $value]);
            } else {
                Setting::create([$key => $value]);
            }
            Cache::forget(self::CACHE_KEY);
        });
    }

    /**
     * 批量设置配置
     * Set multiple settings
     */
    public function setMany(array $settings): void
    {
        DB::transaction(function () use ($settings) {
            $setting = Setting::first();
            if ($setting) {
                $setting->update($settings);
            } else {
                Setting::create($settings);
            }
            Cache::forget(self::CACHE_KEY);
        });
    }

    /**
     * 删除配置（重置为默认值）
     * Delete setting (reset to default)
     */
    public function delete(string $key): bool
    {
        return DB::transaction(function () use ($key) {
            $setting = Setting::first();
            if ($setting) {
                $setting->update([$key => null]);
                Cache::forget(self::CACHE_KEY);
                return true;
            }
            return false;
        });
    }

    /**
     * 获取设置模型实例
     * Get setting model instance
     */
    public function getSettingModel(): ?Setting
    {
        return Setting::first();
    }

    /**
     * 获取站点配置
     * Get site configuration
     */
    public function getSiteConfig(): array
    {
        $settings = $this->getAll();
        return [
            'name' => $settings['name'] ?? config('app.name'),
            'title' => $settings['description'] ?? '',
            'description' => $settings['description'] ?? '',
            'keywords' => $settings['description'] ?? '',
            'logo' => $settings['logo'] ?? '',
            'favicon' => $settings['favicon'] ?? '',
            'copyright' => $settings['copyright'] ?? '',
            'site_url' => $settings['site_url'] ?? '',
            'maintenance' => (bool) ($settings['maintenance'] ?? false),
            'registration' => (bool) ($settings['registration'] ?? true),
            'social_login' => (bool) ($settings['social_login'] ?? true),
            'comments' => (bool) ($settings['comments'] ?? true),
            'author_bio' => (bool) ($settings['author_bio'] ?? true),
            'search' => (bool) ($settings['search'] ?? true),
        ];
    }

    /**
     * 获取SEO配置（从 seo 表读取真实数据）
     * Get SEO configuration (from seo table)
     */
    public function getSeoConfig(): array
    {
        $seo = \App\Models\Seo::getGlobalSeo();

        return [
            'meta_title' => $seo->meta_title ?? '',
            'meta_description' => $seo->meta_description ?? '',
            'meta_keywords' => $seo->meta_keywords ?? '',
            'google_analytics' => $seo->google_analytics ?? '',
            'baidu_analytics' => $seo->baidu_analytics ?? '',
            'sitemap' => (bool) ($seo->sitemap ?? false),
            'robots' => (bool) ($seo->robots ?? false),
            'llm_txt' => (bool) ($seo->llm_txt ?? false),
            'rss_feed' => (bool) ($seo->rss_feed ?? true),
            'canonical_url' => $seo->canonical_url ?? '',
            'og_image' => $seo->og_image ?? '',
            'og_type' => $seo->og_type ?? 'website',
            'twitter_card' => $seo->twitter_card ?? 'summary_large_image',
        ];
    }

    /**
     * 获取评论配置
     * Get comment configuration
     *
     * - requires_approval: true = 需要管理员审核, false = 直接展示
     * - enabled: 评论功能总开关
     */
    public function getCommentConfig(): array
    {
        $settings = $this->getAll();
        return [
            'requires_approval' => (bool) ($settings['comment_approval'] ?? false),
            'enabled' => (bool) ($settings['comments'] ?? true),
            'max_depth' => 3,
        ];
    }

    /**
     * 刷新缓存
     * Refresh cache
     */
    public function refreshCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}