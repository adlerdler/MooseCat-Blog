<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogMiddleware
{
    /**
     * 需要记录的 HTTP 方法
     *
     * @var array
     */
    protected $loggedMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * 排除的路由模式
     *
     * @var array
     */
    protected $excludedRoutes = [
        'login',
        'logout',
        'password.*',
        'api.*',
        'admin/logs.*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // 记录活动日志（仅在请求完成后）
        if ($this->shouldLog($request)) {
            try {
                $this->logActivity($request, $response);
            } catch (\Throwable $e) {
                // 日志记录失败不能影响正常响应
                Log::warning('ActivityLogMiddleware: logActivity failed', [
                    'error' => $e->getMessage(),
                    'url'   => $request->fullUrl(),
                ]);
            }
        }

        return $response;
    }

    /**
     * 判断是否需要记录日志
     *
     * @param Request $request
     * @return bool
     */
    protected function shouldLog(Request $request): bool
    {
        // 排除不需要记录的方法
        if (!in_array($request->method(), $this->loggedMethods)) {
            return false;
        }

        // 排除特定路由
        foreach ($this->excludedRoutes as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        // 只记录已登录用户的操作
        if (!Auth::check()) {
            return false;
        }

        return true;
    }

    /**
     * 记录活动日志
     *
     * @param Request $request
     * @param \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse $response
     * @return void
     */
    protected function logActivity(Request $request, $response): void
    {
        $user = Auth::user();

        $logData = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => $request->route() ? $request->route()->getName() : null,
            'action' => $this->getActionDescription($request),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status_code' => $response->getStatusCode(),
            'request_data' => $this->sanitizeRequestData($request),
            'timestamp' => now()->toDateTimeString(),
        ];

        // 记录到日志文件
        Log::info('User activity', $logData);

        // 记录到 spatie activity_log 表
        try {
            $this->saveToDatabase($logData);
        } catch (\Throwable $e) {
            Log::warning('ActivityLogMiddleware: saveToDatabase failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 获取操作描述
     *
     * @param Request $request
     * @return string
     */
    protected function getActionDescription(Request $request): string
    {
        $routeName = $request->route() ? $request->route()->getName() : '';

        // 动态路由名称映射方案
        $moduleMap = [
            'posts' => '文章', 'categories' => '分类', 'tags' => '标签',
            'videos' => '视频', 'projects' => '项目', 'resources' => '资源',
            'users' => '用户', 'roles' => '角色', 'comments' => '评论',
            'advertisements' => '广告', 'journals' => '日记', 'subscribers' => '订阅者',
            'user-levels' => '用户等级', 'author-profiles' => '作者资料', 'media' => '媒体',
            'settings' => '设置', 'seo' => 'SEO', 'social-links' => '社交链接',
            'i18n' => '国际化', 'email-templates' => '邮件模板', 'mail-config' => '邮件配置',
            'menus' => '菜单', 'front-menu' => '前台菜单',
        ];

        $methodMap = [
            'store' => '创建', 'update' => '更新', 'destroy' => '删除',
        ];

        // 匹配 admin.{module}.{method} 格式
        if (preg_match('/^admin\.([^.]+)\.(store|update|destroy)$/', $routeName, $matches)) {
            $module = $matches[1];
            $method = $matches[2];
            $moduleLabel = $moduleMap[$module] ?? $module;
            $methodLabel = $methodMap[$method] ?? $method;
            return "{$methodLabel}{$moduleLabel}";
        }

        return "{$request->method()} {$routeName}";
    }

    /**
     * 清理请求数据（移除敏感信息并格式化文件对象）
     *
     * @param Request $request
     * @return array
     */
    protected function sanitizeRequestData(Request $request): array
    {
        $data = $request->all();
        return $this->sanitizeValue($data);
    }

    /**
     * 递归清洗值：过滤敏感键名、UploadedFile 文件和普通对象
     *
     * @param mixed $value
     * @return mixed
     */
    protected function sanitizeValue(mixed $value): mixed
    {
        // 1. 如果是数组，递归清洗所有级联键值
        if (is_array($value)) {
            $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key', 'client_secret'];
            $result = [];
            foreach ($value as $key => $val) {
                // 不区分大小写匹配敏感字段
                if (in_array(strtolower((string) $key), $sensitiveFields)) {
                    $result[$key] = '***';
                } else {
                    $result[$key] = $this->sanitizeValue($val);
                }
            }
            return $result;
        }

        // 2. 如果是上传的文件对象，替换为人类可读占位文本，防止 JSON 编码崩溃或数据库膨胀
        if ($value instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            try {
                return sprintf('[File: %s (%s bytes)]', $value->getClientOriginalName(), $value->getSize());
            } catch (\Throwable) {
                return '[File: Uploaded File]';
            }
        }

        // 3. 如果是其他无法被 JSON 序列化的复杂对象，格式化为类名占位符
        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string) $value;
            }
            return '[Object: ' . get_class($value) . ']';
        }

        return $value;
    }

    /**
     * 保存到 spatie/activitylog 的 activity_log 表
     *
     * @param array $logData
     * @return void
     */
    protected function saveToDatabase(array $logData): void
    {
        activity()
            ->causedBy(Auth::user())
            ->withProperties($logData)
            ->inLog('http')
            ->event('http_request')
            ->log($logData['action']);
    }
}