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

        // 前台 404 — Vue SPA 内直接渲染 components/ErrorPage.vue
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if (!$request->expectsJson()) {
                return \Inertia\Inertia::render('ErrorPage', [
                    'errorCode' => 404,
                    'errorPath' => $request->path(),
                ])->toResponse($request)->setStatusCode(404);
            }
        });

        // 后台 403 — Vue SPA 内渲染 Forbidden.vue
        // AccessDeniedHttpException（authorizeResource 失败时抛出）
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if (!$request->expectsJson()) {
                return \Inertia\Inertia::render('Forbidden', [
                    'title' => $e->getMessage() ?: '访问被拒绝',
                ])->toResponse($request)->setStatusCode(403);
            }
        });

        // 后台 403 — HttpException（abort(403) / abort_unless 抛出）
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403 && !$request->expectsJson()) {
                return \Inertia\Inertia::render('Forbidden', [
                    'title' => $e->getMessage() ?: '访问被拒绝',
                ])->toResponse($request)->setStatusCode(403);
            }
        });

        // 未登录 → 跳转登录页
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->header('X-Inertia')) {
                return redirect()->guest(route('login'));
            }
        });
    })->create();