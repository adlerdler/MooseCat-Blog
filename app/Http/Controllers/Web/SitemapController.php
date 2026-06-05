<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Project;
use App\Models\Video;
use App\Models\Category;
use App\Models\Seo;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * SitemapController - 动态生成 sitemap.xml
 * 
 * 自动收集所有已发布内容，生成符合 Sitemap Protocol 0.9 的 XML。
 * 受 SEO 设置中的 sitemap 开关控制。
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        // 检查 sitemap 是否启用
        $seo = Seo::getGlobalSeo();
        if (!$seo || !$seo->sitemap) {
            throw new NotFoundHttpException('Sitemap is not enabled');
        }

        $urls = [];

        // --- 静态页面 ---
        $urls[] = $this->url(route('home'), '1.0', 'daily');
        $urls[] = $this->url(route('blog'), '0.9', 'daily');
        $urls[] = $this->url(route('projects'), '0.8', 'weekly');
        $urls[] = $this->url(route('videos'), '0.8', 'weekly');
        $urls[] = $this->url(route('resources'), '0.7', 'weekly');

        // --- 动态内容：已发布文章 ---
        Post::where('status', 'published')
            ->select('slug', 'updated_at')
            ->latest('updated_at')
            ->each(function ($post) use (&$urls) {
                $urls[] = $this->url(
                    route('posts.detail', $post->slug),
                    '0.8',
                    'weekly',
                    $post->updated_at->toAtomString()
                );
            });

        // --- 动态内容：已完成项目 ---
        Project::where('status', 'completed')
            ->select('slug', 'updated_at')
            ->latest('updated_at')
            ->each(function ($project) use (&$urls) {
                $urls[] = $this->url(
                    route('projects.detail', $project->slug),
                    '0.7',
                    'monthly',
                    $project->updated_at->toAtomString()
                );
            });

        // --- 动态内容：已发布视频 ---
        Video::where('status', 'published')
            ->select('slug', 'updated_at')
            ->latest('updated_at')
            ->each(function ($video) use (&$urls) {
                $urls[] = $this->url(
                    route('videos.detail', $video->slug),
                    '0.7',
                    'monthly',
                    $video->updated_at->toAtomString()
                );
            });

        // --- 分类页面 ---
        Category::where('status', 'active')
            ->select('slug', 'updated_at')
            ->get()
            ->each(function ($category) use (&$urls) {
                $urls[] = $this->url(
                    route('categories.show', $category->slug),
                    '0.6',
                    'weekly',
                    $category->updated_at?->toAtomString()
                );
            });

        $xml = $this->buildXml($urls);

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    private function url(string $loc, string $priority = '0.5', string $changefreq = 'monthly', ?string $lastmod = null): array
    {
        return compact('loc', 'priority', 'changefreq', 'lastmod');
    }

    private function buildXml(array $urls): string
    {
        $items = '';
        foreach ($urls as $url) {
            $items .= "  <url>\n";
            $items .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            if ($url['lastmod']) {
                $items .= '    <lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1, 'UTF-8') . "</lastmod>\n";
            }
            $items .= '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
            $items .= '    <priority>' . $url['priority'] . "</priority>\n";
            $items .= "  </url>\n";
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{$items}</urlset>
XML;
    }
}
