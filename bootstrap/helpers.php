<?php

if (!function_exists('cdn_url')) {
    /**
     * Generate CDN or local URL for assets.
     * 
     * Logic:
     * - cdn disabled → return local asset URL
     * - cdn enabled but cdn_url empty → return local asset URL (Cloudflare auto-accelerates)
     * - cdn enabled and cdn_url filled → return custom CDN URL
     *
     * @param string $path Asset path (e.g. 'storage/images/logo.png')
     * @return string Full URL
     */
    function cdn_url(string $path): string
    {
        $settingService = app(\App\Services\SettingService::class);
        $enabled = $settingService->get('cdn', false);
        $cdnUrl = $settingService->get('cdn_url', '');

        if ($enabled && $cdnUrl) {
            return rtrim($cdnUrl, '/') . '/' . ltrim($path, '/');
        }

        return asset($path);
    }
}
