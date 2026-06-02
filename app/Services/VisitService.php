<?php

namespace App\Services;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
     * 同时做两件事：
     * 1. 递增模型的 views_count 字段（快速计数）
     * 2. 通过多态关联创建 Visit 记录（详细元数据，用于仪表盘分析）
     */
    public function trackModel(Model $model, Request $request): ?Visit
    {
        if (!method_exists($model, 'visits')) {
            // 模型未定义 visits() 多态关联，仅计数
            $model->increment('views_count');
            return null;
        }

        $model->increment('views_count');

        return $model->visits()->create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer'   => $request->header('referer'),
        ]);
    }

    /**
     * 记录普通页面浏览（首页、列表页等非模型页面）
     *
     * 用于仪表盘流量图表的数据来源。
     */
    public function trackPage(string $page, string $title, Request $request): Visit
    {
        return Visit::create([
            'page'       => $page,
            'title'      => $title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer'   => $request->header('referer'),
        ]);
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
