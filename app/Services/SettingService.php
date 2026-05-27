<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

/**
 * SettingService - 设置服务类
 * 
 * 提供系统设置的管理功能，支持配置缓存、站点配置、SEO配置和评论配置的读取与更新。
 * Provides system setting management functionality, supporting configuration caching, 
 * site config, SEO config and comment config reading and updating.
 */
class SettingService
{
    private const CACHE_KEY = 'settings_all';

    /**
     * 获取所有设置（带缓存）
     * Get all settings with cache
     */
    public function getAll(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return Setting::pluck('value', 'key')->toArray();
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
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
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
            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
            Cache::forget(self::CACHE_KEY);
        });
    }

    /**
     * 删除配置
     * Delete setting
     */
    public function delete(string $key): bool
    {
        return DB::transaction(function () use ($key) {
            $result = Setting::where('key', $key)->delete();
            Cache::forget(self::CACHE_KEY);
            return $result > 0;
        });
    }

    /**
     * 获取设置集合
     * Get settings collection
     */
    public function getSettingsCollection(): Collection
    {
        return Setting::all();
    }

    /**
     * 获取站点配置
     * Get site configuration
     */
    public function getSiteConfig(): array
    {
        $settings = $this->getAll();
        return [
            'name' => $settings['site_name'] ?? config('app.name'),
            'title' => $settings['site_title'] ?? '',
            'description' => $settings['site_description'] ?? '',
            'keywords' => $settings['site_keywords'] ?? '',
            'logo' => $settings['site_logo'] ?? '',
            'favicon' => $settings['site_favicon'] ?? '',
        ];
    }

    /**
     * 获取SEO配置
     * Get SEO configuration
     */
    public function getSeoConfig(): array
    {
        $settings = $this->getAll();
        return [
            'meta_title' => $settings['seo_meta_title'] ?? '',
            'meta_description' => $settings['seo_meta_description'] ?? '',
            'meta_keywords' => $settings['seo_meta_keywords'] ?? '',
            'og_title' => $settings['seo_og_title'] ?? '',
            'og_description' => $settings['seo_og_description'] ?? '',
            'og_image' => $settings['seo_og_image'] ?? '',
        ];
    }

    /**
     * 获取评论配置
     * Get comment configuration
     */
    public function getCommentConfig(): array
    {
        $settings = $this->getAll();
        return [
            'auto_approve' => (bool) ($settings['comment_auto_approve'] ?? true),
            'enabled' => (bool) ($settings['comment_enabled'] ?? true),
            'max_depth' => (int) ($settings['comment_max_depth'] ?? 3),
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