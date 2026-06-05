<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Seo;
use Illuminate\Http\Response;

/**
 * RobotsController - 生成 robots.txt
 * 
 * 告诉搜索引擎哪些页面可以抓取，引用 sitemap 位置。
 * Sitemap 引用受 SEO 设置中的 sitemap 开关控制。
 * Robots 本身受 seo.robots 开关控制。
 */
class RobotsController extends Controller
{
    public function index(): Response
    {
        $seo = Seo::getGlobalSeo();
        $robotsEnabled = $seo && $seo->robots;

        // 关闭 robots.txt 时，禁止所有抓取
        if (!$robotsEnabled) {
            return response("User-agent: *\nDisallow: /\n", 200, [
                'Content-Type' => 'text/plain; charset=utf-8',
            ]);
        }

        $baseUrl = rtrim(config('app.url', request()->getSchemeAndHttpHost()), '/');
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

        // Crawl-delay for polite crawling
        $lines[] = '# Crawl delay (seconds)';
        $lines[] = 'Crawl-delay: 1';
        $lines[] = '';

        // Sitemap reference
        if ($sitemapEnabled) {
            $lines[] = '# Sitemap';
            $lines[] = "Sitemap: {$baseUrl}/sitemap.xml";
            $lines[] = '';
        }

        // LLM.txt reference
        if ($llmEnabled) {
            $lines[] = '# AI/LLM content index';
            $lines[] = "Sitemap: {$baseUrl}/llm.txt";
            $lines[] = '';
        }

        $content = implode("\n", $lines);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
