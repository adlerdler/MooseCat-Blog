<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Seo;
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
            $seo = Seo::getGlobalSeo();
            
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
                'google_analytics' => '',
                'baidu_analytics' => '',
                'sitemap' => false,
                'robots' => false,
                'llm_txt' => false,
                'rss_feed' => true,
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'canonical_url' => '',
                'og_type' => 'website',
            ];

            if ($seo) {
                $config['title'] = $seo->meta_title ?: config('app.name');
                $config['description'] = $seo->meta_description ?? '';
                $config['keywords'] = $seo->meta_keywords ?? '';
                $config['author'] = $seo->author ?? '';
                $config['og_title'] = $seo->og_title ?? '';
                $config['og_description'] = $seo->og_description ?? '';
                $config['og_image'] = $seo->og_image ?? '';
                $config['twitter_card'] = $seo->twitter_card ?? 'summary';
                $config['twitter_site'] = $seo->twitter_site ?? '';
                $config['google_analytics'] = $seo->google_analytics ?? '';
                $config['baidu_analytics'] = $seo->baidu_analytics ?? '';
                $config['sitemap'] = $seo->sitemap ?? false;
                $config['robots'] = $seo->robots ?? false;
                $config['llm_txt'] = $seo->llm_txt ?? false;
                $config['rss_feed'] = $seo->rss_feed ?? true;
                $config['meta_title'] = $seo->meta_title ?? '';
                $config['meta_description'] = $seo->meta_description ?? '';
                $config['meta_keywords'] = $seo->meta_keywords ?? '';
                $config['canonical_url'] = $seo->canonical_url ?? '';
                $config['og_type'] = $seo->og_type ?? 'website';
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