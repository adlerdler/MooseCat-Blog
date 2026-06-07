<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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

        // 所有 HTTP 异常统一走 ErrorPage.vue（排除 ValidationException）
        $exceptions->render(function (\Throwable $e, $request) {
            // 验证异常不处理，让 Laravel/Inertia 默认处理
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return null;
            }

            // API 请求返回 JSON
            if ($request->expectsJson()) {
                return null;
            }

            // 获取状态码
            $statusCode = 500;
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                $statusCode = 500;
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
                $statusCode = 500;
            }

            // 调试模式下保存错误详情到 session
            if (config('app.debug')) {
                session()->flash('error_debug', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => collect($e->getTrace())
                        ->take(20)
                        ->map(fn($t) => [
                            'file' => $t['file'] ?? 'unknown',
                            'line' => $t['line'] ?? 0,
                            'function' => $t['function'] ?? 'unknown',
                            'class' => $t['class'] ?? '',
                        ])
                        ->toArray(),
                ]);
            }

            // 重定向到错误路由，确保 Inertia 正常渲染
            return redirect()->to("/error/{$statusCode}?path=" . urlencode($request->path()));
        });

        // 未登录 → 跳转登录页
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->header('X-Inertia')) {
                return redirect()->guest(route('login'));
            }
        });
    })->create();