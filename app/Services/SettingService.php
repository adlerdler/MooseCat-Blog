<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class SettingService
{
    private const CACHE_KEY = 'settings_all';

    public function getAll(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    public function get(string $key, $default = null)
    {
        $settings = $this->getAll();
        return $settings[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        DB::transaction(function () use ($key, $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            Cache::forget(self::CACHE_KEY);
        });
    }

    public function setMany(array $settings): void
    {
        DB::transaction(function () use ($settings) {
            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
            Cache::forget(self::CACHE_KEY);
        });
    }

    public function delete(string $key): bool
    {
        return DB::transaction(function () use ($key) {
            $result = Setting::where('key', $key)->delete();
            Cache::forget(self::CACHE_KEY);
            return $result > 0;
        });
    }

    public function getSettingsCollection(): Collection
    {
        return Setting::all();
    }

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

    public function getCommentConfig(): array
    {
        $settings = $this->getAll();
        return [
            'auto_approve' => (bool) ($settings['comment_auto_approve'] ?? true),
            'enabled' => (bool) ($settings['comment_enabled'] ?? true),
            'max_depth' => (int) ($settings['comment_max_depth'] ?? 3),
        ];
    }

    public function refreshCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
