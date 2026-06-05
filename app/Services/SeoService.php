<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use App\Models\Project;
use App\Models\Video;
use App\Models\Category;
use App\Models\Seo;
use App\Models\PageSeo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

/**
 * SeoService - SEO 管理服务类
 *
 * 提供全局 SEO 配置、页面级 SEO 的 CRUD 功能，以及 SEO 静态文件生成。
 * Provides global SEO config, page-level SEO CRUD, and SEO static file generation.
 */
class SeoService
{
    public function __construct() {}

    // ==================== 全局 SEO 配置 ====================

    /**
     * 获取全局 SEO 配置
     * Get global SEO configuration.
     */
    public function getGlobalSeo(): ?Seo
    {
        return Seo::getGlobalSeo();
    }

    /**
     * 更新或创建全局 SEO 配置
     * Update or create global SEO configuration.
     */
    public function updateGlobalSeo(array $data): Seo
    {
        return Seo::updateGlobalSeo($data);
    }

    // ==================== 页面级 SEO ====================

    /**
     * 获取所有页面 SEO 配置
     * Get all page-level SEO configurations.
     */
    public function getAllPageSeo(): Collection
    {
        return PageSeo::orderBy('page_key')->get();
    }

    /**
     * 创建页面 SEO 配置
     * Create a page-level SEO configuration.
     */
    public function createPageSeo(array $data): PageSeo
    {
        return PageSeo::create($data);
    }

    /**
     * 更新页面 SEO 配置
     * Update a page-level SEO configuration.
     */
    public function updatePageSeo(PageSeo $pageSeo, array $data): PageSeo
    {
        $pageSeo->update($data);
        return $pageSeo;
    }

    /**
     * 删除页面 SEO 配置
     * Delete a page-level SEO configuration.
     */
    public function deletePageSeo(PageSeo $pageSeo): void
    {
        $pageSeo->delete();
    }

    // ==================== SEO 静态文件生成 ====================

    /**
     * 根据当前 SEO 设置生成/删除所有静态文件
     */
    public function regenerateSeoFiles(): void
    {
        $seo = Seo::getGlobalSeo();

        $this->generateRobots($seo);
        $this->generateLlmTxt($seo);
        $this->generateSitemap($seo);
    }

    /**
     * 生成 robots.txt
     */
    public function generateRobots(?Seo $seo = null): void
    {
        $seo ??= Seo::getGlobalSeo();

        if (!$seo || !$seo->robots) {
            $this->deleteSeoFile('robots.txt');
            return;
        }

        $baseUrl = rtrim(config('app.url'), '/');
        $sitemapEnabled = $seo && $seo->sitemap;
        $llmEnabled = $seo && $seo->llm_txt;

        $lines = [];
        $lines[] = '# robots.txt - Generated dynamically';
        $lines[] = '# https://www.robotstxt.org/';
        $lines[] = '';
        $lines[] = 'User-agent: *';
        $lines[] = 'Allow: /';
        $lines[] = '';
        $lines[] = '# Disallow admin and API routes';
        $lines[] = 'Disallow: /admin/';
        $lines[] = 'Disallow: /api/';
        $lines[] = 'Disallow: /login';
        $lines[] = 'Disallow: /register';
        $lines[] = '';
        $lines[] = '# Crawl delay (seconds)';
        $lines[] = 'Crawl-delay: 1';
        $lines[] = '';

        if ($sitemapEnabled) {
            $lines[] = '# Sitemap';
            $lines[] = "Sitemap: {$baseUrl}/sitemap.xml";
            $lines[] = '';
        }

        if ($llmEnabled) {
            $lines[] = '# AI/LLM content index';
            $lines[] = "Sitemap: {$baseUrl}/llm.txt";
            $lines[] = '';
        }

        $this->writeSeoFile('robots.txt', implode("\n", $lines));
    }

    /**
     * 生成 llm.txt
     */
    public function generateLlmTxt(?Seo $seo = null): void
    {
        $seo ??= Seo::getGlobalSeo();

        if (!$seo || !$seo->llm_txt) {
            $this->deleteSeoFile('llm.txt');
            return;
        }

        $siteName = config('app.name', 'My Blog');
        $siteUrl = rtrim(config('app.url'), '/');
        $siteDescription = $seo->meta_description ?? '';
        $canonicalUrl = $seo->canonical_url ?: $siteUrl;

        $posts = Post::where('status', 'published')
            ->select('title', 'slug', 'excerpt', 'published_at')
            ->latest('published_at')
            ->limit(50)
            ->get();

        $projects = Project::where('status', 'completed')
            ->select('title', 'slug', 'description')
            ->latest('updated_at')
            ->limit(20)
            ->get();

        $videos = Video::where('status', 'published')
            ->select('title', 'slug', 'description')
            ->latest('published_at')
            ->limit(20)
            ->get();

        $categories = Category::where('status', 'active')
            ->select('name', 'slug', 'description')
            ->get();

        $lines = [];
        $lines[] = "# {$siteName}";
        $lines[] = '';
        $lines[] = $siteDescription ?: 'A personal blog sharing knowledge and projects.';
        $lines[] = '';
        $lines[] = "URL: {$siteUrl}";
        $lines[] = "Canonical: {$canonicalUrl}";
        $lines[] = '';
        $lines[] = '---';
        $lines[] = '';

        $lines[] = '## Site Overview';
        $lines[] = '';
        $lines[] = "- Total Posts: {$posts->count()}";
        $lines[] = "- Total Projects: {$projects->count()}";
        $lines[] = "- Total Videos: {$videos->count()}";
        $lines[] = "- Total Categories: {$categories->count()}";
        $lines[] = '';
        $lines[] = '---';
        $lines[] = '';

        if ($categories->isNotEmpty()) {
            $lines[] = '## Categories';
            $lines[] = '';
            foreach ($categories as $cat) {
                $desc = $cat->description ? " — {$cat->description}" : '';
                $lines[] = "- [{$cat->name}]({$siteUrl}/categories/{$cat->slug}){$desc}";
            }
            $lines[] = '';
            $lines[] = '---';
            $lines[] = '';
        }

        if ($posts->isNotEmpty()) {
            $lines[] = '## Latest Blog Posts';
            $lines[] = '';
            foreach ($posts as $post) {
                $excerpt = $post->excerpt ? " — {$post->excerpt}" : '';
                $date = $post->published_at ? " (`{$post->published_at->format('Y-m-d')}`)" : '';
                $lines[] = "- [{$post->title}]({$siteUrl}/blog/{$post->slug}){$date}{$excerpt}";
            }
            $lines[] = '';
            $lines[] = '---';
            $lines[] = '';
        }

        if ($projects->isNotEmpty()) {
            $lines[] = '## Projects';
            $lines[] = '';
            foreach ($projects as $project) {
                $desc = $project->description ? " — {$project->description}" : '';
                $lines[] = "- [{$project->title}]({$siteUrl}/projects/{$project->slug}){$desc}";
            }
            $lines[] = '';
            $lines[] = '---';
            $lines[] = '';
        }

        if ($videos->isNotEmpty()) {
            $lines[] = '## Videos';
            $lines[] = '';
            foreach ($videos as $video) {
                $desc = $video->description ? " — {$video->description}" : '';
                $lines[] = "- [{$video->title}]({$siteUrl}/videos/{$video->slug}){$desc}";
            }
            $lines[] = '';
        }

        $this->writeSeoFile('llm.txt', implode("\n", $lines));
    }

    /**
     * 生成 sitemap.xml
     */
    public function generateSitemap(?Seo $seo = null): void
    {
        $seo ??= Seo::getGlobalSeo();

        if (!$seo || !$seo->sitemap) {
            $this->deleteSeoFile('sitemap.xml');
            return;
        }

        $baseUrl = rtrim(config('app.url'), '/');
        $now = now()->toIso8601String();

        $urls = [];

        // 首页
        $urls[] = [
            'loc' => $baseUrl,
            'lastmod' => $now,
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // 分类
        $categories = Category::where('status', 'active')->get();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => "{$baseUrl}/categories/{$category->slug}",
                'lastmod' => $category->updated_at->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ];
        }

        // 已发布文章
        $posts = Post::where('status', 'published')
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->get();
        foreach ($posts as $post) {
            $urls[] = [
                'loc' => "{$baseUrl}/blog/{$post->slug}",
                'lastmod' => $post->updated_at->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // 已完成项目
        $projects = Project::where('status', 'completed')->get();
        foreach ($projects as $project) {
            $urls[] = [
                'loc' => "{$baseUrl}/projects/{$project->slug}",
                'lastmod' => $project->updated_at->toIso8601String(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        // 已发布视频
        $videos = Video::where('status', 'published')
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->get();
        foreach ($videos as $video) {
            $urls[] = [
                'loc' => "{$baseUrl}/videos/{$video->slug}",
                'lastmod' => $video->updated_at->toIso8601String(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        // 生成 XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url['loc']}</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        $this->writeSeoFile('sitemap.xml', $xml);
    }

    /**
     * 写入 SEO 静态文件
     */
    protected function writeSeoFile(string $filename, string $content): void
    {
        $path = public_path($filename);
        File::put($path, $content);
    }

    protected function deleteSeoFile(string $filename): void
    {
        $path = public_path($filename);
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
