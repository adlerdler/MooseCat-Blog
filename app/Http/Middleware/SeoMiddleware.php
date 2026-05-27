<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SeoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $seoConfig = $this->getSeoConfig();
        
        if ($request->isMethod('GET') && !$request->ajax()) {
            $this->shareSeoData($seoConfig);
        }
        
        return $next($request);
    }

    /**
     * 获取 SEO 配置
     *
     * @return array
     */
    protected function getSeoConfig(): array
    {
        return Cache::remember('seo_config', 3600, function () {
            $settings = Setting::where('key', 'like', 'seo_%')->get();
            
            $config = [
                'title' => config('app.name'),
                'description' => '',
                'keywords' => '',
                'author' => '',
                'og_title' => '',
                'og_description' => '',
                'og_image' => '',
                'twitter_card' => 'summary',
                'twitter_site' => '',
            ];

            foreach ($settings as $setting) {
                $key = str_replace('seo_', '', $setting->key);
                if (array_key_exists($key, $config)) {
                    $config[$key] = $setting->value;
                }
            }

            return $config;
        });
    }

    /**
     * 共享 SEO 数据到视图
     *
     * @param array $seoConfig
     * @return void
     */
    protected function shareSeoData(array $seoConfig): void
    {
        if (class_exists('\Inertia\Inertia')) {
            \Inertia\Inertia::share([
                'seo' => $seoConfig,
            ]);
        }
        
        view()->share('seo', $seoConfig);
    }
}