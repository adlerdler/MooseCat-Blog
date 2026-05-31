<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class LogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_logs');
    }

    /**
     * Display the logs page.
     */
    public function index(Request $request): Response
    {
        $activities = Activity::with('causer')
            ->where('log_name', '!=', 'http')  // 排除中间件条目，模型事件已包含完整信息
            ->orderBy('created_at', 'desc')
            ->get();

        $logs = $activities->map(fn (Activity $activity) => $this->formatForFrontend($activity));

        return Inertia::render('admin/Logs', [
            'logs' => $logs,
        ]);
    }

    /**
     * Clear all logs.
     */
    public function clear(): \Illuminate\Http\RedirectResponse
    {
        Activity::truncate();

        return back()->with('success', '日志已清空');
    }

    /**
     * Delete a specific log entry.
     */
    public function destroy(string $id): \Illuminate\Http\RedirectResponse
    {
        Activity::findOrFail($id)->delete();

        return back()->with('success', '日志已删除');
    }

    /**
     * Map spatie Activity model to frontend-expected format.
     *
     * Frontend expects: action, module, user, details, ip, user_agent, changes, created_at
     * Spatie provides:  event,  log_name, (causer), description, ip_address, user_agent, properties, created_at
     *
     * Two sources of activity:
     *  - log_name='http': HTTP 中间件条目 → description 即操作名
     *  - log_name=其他: 模型 LogsActivity trait → event='created'/'updated'/'deleted' 需翻译
     */
    private function formatForFrontend(Activity $activity): array
    {
        $properties = $activity->properties->toArray();

        if ($activity->log_name === 'http') {
            // HTTP 中间件 → action 用 description，module 从 route 提取
            $action  = $activity->description ?: ($activity->event ?: 'unknown');
            $module  = $this->moduleLabel($properties['route'] ?? $activity->log_name);
            $details = ($properties['method'] ?? '') . ' ' . ($properties['url'] ?? '');
        } else {
            // 模型事件 → 翻译 event + log_name 为中文操作名
            $action  = $this->translateEvent($activity->event, $activity->log_name);
            $module  = $this->moduleLabel($activity->log_name);
            $details = $activity->description ?: $action;
        }

        // spatie stores attributes under 'attributes' / 'old' keys; flatten for frontend
        $changes = null;
        if (! empty($properties['attributes']) || ! empty($properties['old'])) {
            $changes = [
                'before' => $properties['old'] ?? null,
                'after'  => $properties['attributes'] ?? null,
            ];
        }

        return [
            'id'         => $activity->id,
            'action'     => $action,
            'module'     => $module,
            'user'       => $activity->causer?->name ?? 'System',
            'details'    => $details,
            'ip'         => $activity->ip_address ?? ($properties['ip_address'] ?? ''),
            'user_agent' => $activity->user_agent ?? ($properties['user_agent'] ?? ''),
            'changes'    => $changes,
            'created_at' => $activity->created_at?->toISOString(),
        ];
    }

    /**
     * Translate spatie generic event name + log_name to Chinese action description.
     * e.g. event='created' + log_name='users' → '创建用户'
     */
    private function translateEvent(?string $event, ?string $logName): string
    {
        $eventLabels = [
            'created'      => '创建',
            'updated'      => '更新',
            'deleted'      => '删除',
            'restored'     => '恢复',
            'forceDeleted' => '永久删除',
        ];

        $eventLabel = $eventLabels[$event] ?? ($event ?: '操作');
        $moduleLabel = $this->moduleLabel($logName);

        return $moduleLabel ? "{$eventLabel}{$moduleLabel}" : $eventLabel;
    }

    /**
     * Map module slug to Chinese label.
     */
    private function moduleLabel(?string $key): string
    {
        $map = [
            'posts'           => '文章',
            'categories'      => '分类',
            'tags'            => '标签',
            'videos'          => '视频',
            'projects'        => '项目',
            'resources'       => '资源',
            'users'           => '用户',
            'roles'           => '角色',
            'comments'        => '评论',
            'advertisements'  => '广告',
            'journals'        => '日记',
            'subscribers'     => '订阅者',
            'user-levels'     => '用户等级',
            'author-profiles' => '作者资料',
            'media'           => '媒体',
            'settings'        => '设置',
            'seo'             => 'SEO',
            'social-links'    => '社交链接',
            'i18n'            => '国际化',
            'email-templates' => '邮件模板',
            'mail-config'     => '邮件配置',
            'menus'           => '菜单',
            'footer-links'    => '页脚链接',
            'notifications'   => '通知',
            'front-menu'      => '前台菜单',
            'http'            => '系统',
            'default'         => '系统',
        ];

        return $map[$key] ?? ($key ?? '');
    }
}
