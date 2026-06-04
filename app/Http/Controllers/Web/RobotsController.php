<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * RobotsController - 生成 robots.txt
 * 
 * 告诉搜索引擎哪些页面可以抓取，引用 sitemap 位置。
 */
class RobotsController extends Controller
{
    public function index(): Response
    {
        $baseUrl = rtrim(config('app.url', request()->getSchemeAndHttpHost()), '/');
        $sitemapUrl = $baseUrl . '/sitemap.xml';

        $content = <<<TXT
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/

Sitemap: {$sitemapUrl}
TXT;

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
