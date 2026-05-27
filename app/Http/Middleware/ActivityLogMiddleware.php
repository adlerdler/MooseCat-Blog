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
            $this->logActivity($request, $response);
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
        Log::channel('activity')->info('User activity', $logData);

        // 如果有 ActivityLog 模型，也可以保存到数据库
        $this->saveToDatabase($logData);
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
        
        $actions = [
            'admin.posts.store' => '创建文章',
            'admin.posts.update' => '更新文章',
            'admin.posts.destroy' => '删除文章',
            'admin.categories.store' => '创建分类',
            'admin.categories.update' => '更新分类',
            'admin.categories.destroy' => '删除分类',
            'admin.tags.store' => '创建标签',
            'admin.tags.update' => '更新标签',
            'admin.tags.destroy' => '删除标签',
            'admin.users.store' => '创建用户',
            'admin.users.update' => '更新用户',
            'admin.users.destroy' => '删除用户',
            'admin.roles.store' => '创建角色',
            'admin.roles.update' => '更新角色',
            'admin.roles.destroy' => '删除角色',
        ];

        return $actions[$routeName] ?? "执行操作: {$routeName}";
    }

    /**
     * 清理请求数据（移除敏感信息）
     *
     * @param Request $request
     * @return array
     */
    protected function sanitizeRequestData(Request $request): array
    {
        $data = $request->all();
        
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***';
            }
        }

        return $data;
    }

    /**
     * 保存到数据库（如果存在 ActivityLog 模型）
     *
     * @param array $logData
     * @return void
     */
    protected function saveToDatabase(array $logData): void
    {
        if (class_exists('\App\Models\ActivityLog')) {
            \App\Models\ActivityLog::create([
                'user_id' => $logData['user_id'],
                'action' => $logData['action'],
                'url' => $logData['url'],
                'method' => $logData['method'],
                'ip_address' => $logData['ip_address'],
                'user_agent' => $logData['user_agent'],
                'status_code' => $logData['status_code'],
                'meta' => json_encode($logData),
            ]);
        }
    }
}