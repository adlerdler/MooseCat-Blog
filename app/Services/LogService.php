<?php

declare(strict_types=1);

namespace App\Services;

use Spatie\Activitylog\Models\Activity;

/**
 * LogService - 活动日志服务类
 *
 * 提供 Spatie Activity Log 的查询、格式化、删除和清空功能。
 * Provides activity log querying, formatting, deletion and clearing.
 */
class LogService
{
    /**
     * 模块中文标签映射
     * Module Chinese label mapping.
     */
    private const MODULE_LABELS = [
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

    /**
     * 事件名中文标签映射
     * Event name Chinese label mapping.
     */
    private const EVENT_LABELS = [
        'created'      => '创建',
        'updated'      => '更新',
        'deleted'      => '删除',
        'restored'     => '恢复',
        'forceDeleted' => '永久删除',
    ];

    /**
     * 获取所有活动日志（排除 HTTP 中间件日志）
     * Get all activity logs excluding HTTP middleware entries.
     */
    public function getAll(): array
    {
        return Activity::with('causer')
            ->where('log_name', '!=', 'http')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn(Activity $activity) => $this->formatForFrontend($activity))
            ->toArray();
    }

    /**
     * 删除单条日志
     * Delete a single log entry.
     */
    public function delete(string $id): void
    {
        Activity::findOrFail($id)->delete();
    }

    /**
     * 清空所有日志
     * Clear all logs.
     */
    public function clear(): void
    {
        Activity::truncate();
    }

    /**
     * 将 Spatie Activity 模型格式化为前端期望的结构
     * Format Spatie Activity model to frontend-expected structure.
     *
     * 前端期望: action, module, user, details, ip, user_agent, changes, created_at
     * Spatie 提供: event, log_name, causer, description, ip_address, user_agent, properties, created_at
     */
    private function formatForFrontend(Activity $activity): array
    {
        $properties = $activity->properties->toArray();

        if ($activity->log_name === 'http') {
            $action  = $activity->description ?: ($activity->event ?: 'unknown');
            $module  = $this->moduleLabel($properties['route'] ?? $activity->log_name);
            $details = ($properties['method'] ?? '') . ' ' . ($properties['url'] ?? '');
        } else {
            $action  = $this->translateEvent($activity->event, $activity->log_name);
            $module  = $this->moduleLabel($activity->log_name);
            $details = $activity->description ?: $action;
        }

        // Spatie 将新旧属性分别存储在 'attributes' / 'old' 键下
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
     * 将 Spatie 通用事件名 + log_name 翻译为中文操作描述
     * Translate Spatie generic event name + log_name to Chinese action description.
     * 例如: event='created' + log_name='users' → '创建用户'
     */
    private function translateEvent(?string $event, ?string $logName): string
    {
        $eventLabel  = self::EVENT_LABELS[$event] ?? ($event ?: '操作');
        $moduleLabel = $this->moduleLabel($logName);

        return $moduleLabel ? "{$eventLabel}{$moduleLabel}" : $eventLabel;
    }

    /**
     * 将模块标识映射为中文标签
     * Map module slug to Chinese label.
     */
    private function moduleLabel(?string $key): string
    {
        return self::MODULE_LABELS[$key] ?? ($key ?? '');
    }
}
