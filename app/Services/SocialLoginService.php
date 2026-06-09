<?php

namespace App\Services;

use App\Models\SocialLoginConfig;
use Illuminate\Support\Facades\Cache;

class SocialLoginService
{
    public function __construct(
        protected CacheService $cacheService
    ) {}

    /**
     * 获取指定 Provider 的配置（带缓存）
     */
    public function getConfig(string $provider): ?SocialLoginConfig
    {
        return $this->cacheService->remember("social_login:{$provider}", function () use ($provider) {
            return SocialLoginConfig::where('provider', $provider)
                ->where('enabled', true)
                ->first();
        });
    }

    /**
     * 获取所有已启用的 Provider 列表
     */
    public function getEnabledProviders(): array
    {
        return $this->cacheService->remember('social_login:enabled', function () {
            return SocialLoginConfig::where('enabled', true)
                ->get(['provider', 'name'])
                ->toArray();
        });
    }

    /**
     * 获取所有 Provider（含未启用的，供后台管理用）
     */
    public function getAllConfigs(): array
    {
        return SocialLoginConfig::orderBy('provider')->get()->toArray();
    }

    /**
     * 更新配置并清除缓存
     */
    public function updateConfig(string $provider, array $data): SocialLoginConfig
    {
        $config = SocialLoginConfig::updateOrCreate(
            ['provider' => $provider],
            $data
        );

        $this->cacheService->forget("social_login:{$provider}");
        $this->cacheService->forget('social_login:enabled');

        return $config;
    }

    /**
     * 获取 OAuth redirect URI
     */
    public function getRedirectUri(string $provider): string
    {
        $config = $this->getConfig($provider);
        if ($config && $config->redirect_uri) {
            return $config->redirect_uri;
        }

        return url("/auth/{$provider}/callback");
    }
}
