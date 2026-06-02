<?php

namespace App\Http\Middleware;

use App\Services\VisitService;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PageVisitMiddleware - 前台页面访问记录中间件
 *
 * 自动记录所有前台 GET 请求的页面访问数据到 visits 表。
 * 过滤机器人爬虫，避免噪音数据。
 */
class PageVisitMiddleware
{
    public function __construct(
        protected VisitService $visitService,
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 仅记录 GET 请求
        if ($request->method() !== 'GET') {
            return $response;
        }

        // 排除管理后台
        if ($request->is('admin*')) {
            return $response;
        }

        // 排除机器人
        if ($this->visitService->isBot($request)) {
            return $response;
        }

        // 排除纯 API/AJAX 请求，但保留 Inertia.js 页面导航
        if (!$request->header('X-Inertia') && ($request->expectsJson() || $request->ajax())) {
            return $response;
        }

        $routeName = $request->route()?->getName() ?? '';
        $page = $request->path() ?: '/';

        // 检测路由中是否有 Eloquent 模型绑定（如 /blog/{post}、/projects/{id}）
        // 有则记录 visitable_id/visitable_type，无则仅记录 page/title
        $model = $this->resolveRouteModel($request);
        if ($model) {
            $this->visitService->trackModel($model, $request);
        } else {
            $title = $this->resolvePageTitle($routeName, $page);
            $this->visitService->trackPage($page, $title, $request);
        }

        return $response;
    }

    /**
     * 从路由参数中提取 Eloquent 模型实例
     * Laravel 路由模型绑定会将解析好的模型注入到路由参数中
     */
    private function resolveRouteModel(Request $request): ?Model
    {
        $route = $request->route();
        if (!$route) return null;

        foreach ($route->parameters() as $param) {
            if ($param instanceof Model) {
                return $param;
            }
        }

        return null;
    }

    /**
     * 根据路由名称解析页面标题
     */
    private function resolvePageTitle(string $routeName, string $path): string
    {
        $map = [
            'home'            => 'Home',
            'blog'            => 'Blog',
            'projects'        => 'Projects',
            'projects.detail' => 'Project Detail',
            'resources'       => 'Resources',
            'videos'          => 'Videos',
            'videos.detail'   => 'Video Detail',
            'posts.detail'    => 'Post Detail',
            'posts.index'     => 'Posts',
            'author'          => 'Author',
            'categories.index' => 'Categories',
            'categories.show' => 'Category Detail',
            'tags.show'       => 'Tag Detail',
        ];

        if (!empty($routeName) && isset($map[$routeName])) {
            return $map[$routeName];
        }

        // Fallback: 用路径生成标题
        $segments = array_filter(explode('/', trim($path, '/')));
        if (empty($segments)) return 'Home';

        return ucfirst(implode(' ', $segments));
    }
}
