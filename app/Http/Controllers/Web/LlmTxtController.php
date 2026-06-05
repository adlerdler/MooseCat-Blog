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
 * LlmTxtController - 生成 llm.txt
 * 
 * 面向 AI 爬虫（ChatGPT、Claude、Perplexity 等）的内容索引文件。
 * 参考 https://llmstxt.org/ 标准。
 */
class LlmTxtController extends Controller
{
    public function index(): Response
    {
        $seo = Seo::getGlobalSeo();
        if (!$seo || !$seo->llm_txt) {
            throw new NotFoundHttpException('llm.txt is not enabled');
        }

        $siteName = config('app.name', 'My Blog');
        $siteUrl = rtrim(config('app.url', request()->getSchemeAndHttpHost()), '/');
        $siteDescription = $seo->meta_description ?? '';
        $canonicalUrl = $seo->canonical_url ?: $siteUrl;

        // 收集内容摘要
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

        // 站点概览
        $lines[] = '## Site Overview';
        $lines[] = '';
        $lines[] = "- Total Posts: {$posts->count()}";
        $lines[] = "- Total Projects: {$projects->count()}";
        $lines[] = "- Total Videos: {$videos->count()}";
        $lines[] = "- Total Categories: {$categories->count()}";
        $lines[] = '';
        $lines[] = '---';
        $lines[] = '';

        // 分类
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

        // 文章
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

        // 项目
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

        // 视频
        if ($videos->isNotEmpty()) {
            $lines[] = '## Videos';
            $lines[] = '';
            foreach ($videos as $video) {
                $desc = $video->description ? " — {$video->description}" : '';
                $lines[] = "- [{$video->title}]({$siteUrl}/videos/{$video->slug}){$desc}";
            }
            $lines[] = '';
        }

        $content = implode("\n", $lines);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
