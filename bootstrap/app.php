<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\ActivityLogMiddleware::class,
            \App\Http\Middleware\PageVisitMiddleware::class,
            \App\Http\Middleware\SeoMiddleware::class,
        ]);
        $middleware->alias([
            'maintenance'  => \App\Http\Middleware\CheckMaintenanceMode::class,
            'permission'   => \App\Http\Middleware\CheckPermission::class,
            'registration' => \App\Http\Middleware\CheckRegistrationEnabled::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '/api/send-verification-code',
            '/api/captcha',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([
            //
        ]);

        // 验证异常交给 Inertia 处理（不跳转到错误页面）
        $exceptions->dontFlash([
            'password',
            'password_confirmation',
        ]);

        // 所有 HTTP 异常统一走 ErrorPage.vue（排除 ValidationException、AuthenticationException 和 AccessDeniedException）
        $exceptions->render(function (\Throwable $e, $request) {
            // 验证异常不处理，让 Laravel/Inertia 默认处理
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return null;
            }

            // 认证异常不处理，让后面的 AuthenticationException 处理器处理
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return null;
            }

            // 403 权限异常 → 跳转后台无权限页面
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
                || $e instanceof \Illuminate\Auth\Access\AuthorizationException
                || $e instanceof \Spatie\Permission\Exceptions\UnauthorizedException
                || ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface && $e->getStatusCode() === 403)) {
                // 后台路由请求跳转 /admin/forbidden
                if (str_starts_with($request->path(), 'admin')) {
                    return redirect()->route('admin.forbidden');
                }
                // 前台 403 继续走 ErrorPage
            }

            // API 请求返回 JSON
            if ($request->expectsJson()) {
                return null;
            }

            // 获取状态码
            $statusCode = 500;
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();
            } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $statusCode = 404;
            } elseif ($e instanceof \Symfony\Component\Routing\Exception\RouteNotFoundException) {
                $statusCode = 404;
            }

            // 404/403/500/503 以外的状态码走默认处理
            if (!in_array($statusCode, [404, 403, 500, 503])) {
                return null;
            }

            // 重定向到错误页面路由，确保 Inertia 正常渲染
            return redirect()->to("/error/{$statusCode}?path=" . urlencode($request->path()));
        });

        // 未登录 → 跳转登录页
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            // 后台路由未登录 → 跳转后台登录页
            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()->guest(route('login'));
            }
            // 前台路由未登录 → 跳转前台登录页
            if ($request->header('X-Inertia')) {
                return redirect()->guest(route('front.login'));
            }
        });
    })->create();