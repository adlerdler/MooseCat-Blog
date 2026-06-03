<?php

namespace App\Services;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * VisitService - 浏览追踪服务
 *
 * 负责统一记录页面浏览和模型浏览数据到 visits 表，
 * 同时维护各模型的 views_count 计数器。
 */
class VisitService
{
    /**
     * 记录模型浏览（文章/视频/项目/资源详情页）
     *
     * 去重：RateLimiter，同一 IP + 同一模型 10 分钟内只允许 1 次
     */
    public function trackModel(Model $model, Request $request): ?Visit
    {
        if (!method_exists($model, 'visits')) {
            Log::info('trackModel_no_visits_relation', [
                'model' => get_class($model),
                'id' => $model->getKey(),
            ]);
            $model->increment('views_count');
            return null;
        }

        $ip = $request->ip();
        $key = 'visit:' . get_class($model) . ':' . $model->getKey() . ':' . $ip;

        // RateLimiter 内部用 Cache 驱动，10 分钟 decay，最多 1 次
        if (RateLimiter::tooManyAttempts($key, 1)) {
            Log::info('trackModel_skip_ratelimit', [
                'model' => get_class($model),
                'id' => $model->getKey(),
                'key' => $key,
            ]);
            return null;
        }
        RateLimiter::hit($key, 600);

        try {
            $model->increment('views_count');
            $visit = $model->visits()->create([
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'referrer'   => $request->header('referer'),
            ]);
            Log::info('trackModel_recorded', [
                'model' => get_class($model),
                'id' => $model->getKey(),
                'visit_id' => $visit->id,
                'key' => $key,
            ]);
            return $visit;
        } catch (\Throwable $e) {
            Log::error("trackModel_failed", [
                'model' => get_class($model),
                'id'    => $model->getKey(),
                'error' => $e->getMessage(),
            ]);
            RateLimiter::clear($key);
            return null;
        }
    }

    /**
     * 记录普通页面浏览（首页、列表页等非模型页面）
     *
     * 去重：RateLimiter，同一 IP + 同一 page 10 分钟内只允许 1 次
     */
    public function trackPage(string $page, string $title, Request $request): Visit
    {
        $ip = $request->ip();
        $key = 'page:' . $page . ':' . $ip;

        if (RateLimiter::tooManyAttempts($key, 1)) {
            Log::info('trackPage_skip_ratelimit', [
                'page' => $page,
                'key' => $key,
            ]);
            return new Visit(['page' => $page, 'title' => $title]);
        }
        RateLimiter::hit($key, 600);

        try {
            $visit = Visit::create([
                'page'       => $page,
                'title'      => $title,
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'referrer'   => $request->header('referer'),
            ]);
            Log::info('trackPage_recorded', [
                'page' => $page,
                'visit_id' => $visit->id,
                'key' => $key,
            ]);
            return $visit;
        } catch (\Throwable $e) {
            Log::error("trackPage_failed", [
                'page'  => $page,
                'error' => $e->getMessage(),
            ]);
            RateLimiter::clear($key);
            return new Visit(['page' => $page, 'title' => $title]);
        }
    }

    /**
     * 判断是否为机器人爬虫
     */
    public function isBot(Request $request): bool
    {
        $ua = strtolower($request->userAgent() ?? '');
        $botPatterns = ['bot', 'crawler', 'spider', 'slurp', 'curl', 'wget', 'python', 'go-http'];

        foreach ($botPatterns as $pattern) {
            if (str_contains($ua, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取今日访问统计
     */
    public function getTodayStats(): array
    {
        return [
            'total_visits'   => Visit::today()->count(),
            'unique_ips'     => Visit::today()->distinct('ip_address')->count(),
            'total_visits_all' => Visit::count(),
        ];
    }
}
