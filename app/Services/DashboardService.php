<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Project;
use App\Models\Resource;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\Video;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

/**
 * DashboardService - 仪表盘真实数据服务
 *
 * 从数据库查询仪表盘所需的全部数据，并转换为前端 useAdminStats.js
 * 所期望的 camelCase 字段格式。
 */
class DashboardService
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }
    /**
     * 获取仪表盘全部数据
     */
    public function getDashboardData(): array
    {
        return [
            'posts'         => $this->getPostsData(),
            'videos'        => $this->getVideosData(),
            'projects'      => $this->getProjectsData(),
            'resources'     => $this->getResourcesData(),
            'users'         => $this->getUsersData(),
            'categories'    => $this->getCategoriesData(),
            'comments'      => $this->getCommentsData(),
            'tags'          => $this->getTagsData(),
            'taggables'     => $this->getTaggablesData(),
            'visits'        => $this->getVisitsData(),
            'dailyTraffic'  => $this->getDailyTrafficData(),
            'userLevels'    => $this->getUserLevelsData(),
            'roles'         => $this->getRolesData(),
            'logs'          => $this->getLogsData(),
            'periodChanges' => $this->getPeriodChanges(),
        ];
    }

    // ─── 单项数据查询 ────────────────────────────────────────────

    /**
     * 文章数据（最新 50 篇，含分类关联）
     */
    public function getPostsData(): array
    {
        return Post::with('category')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(fn ($post) => $this->snakeToCamel($post->toArray()))
            ->toArray();
    }

    /**
     * 视频数据
     */
    public function getVideosData(): array
    {
        return Video::orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($v) => $this->snakeToCamel($v->toArray()))
            ->toArray();
    }

    /**
     * 项目数据
     */
    public function getProjectsData(): array
    {
        return Project::orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($p) => $this->snakeToCamel($p->toArray()))
            ->toArray();
    }

    /**
     * 资源数据
     */
    public function getResourcesData(): array
    {
        return Resource::orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($r) => $this->snakeToCamel($r->toArray()))
            ->toArray();
    }

    /**
     * 用户数据
     */
    public function getUsersData(): array
    {
        return User::orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                $data = $this->snakeToCamel($user->toArray());
                // 确保 level_id 保持 snake_case（useAdminStats 中 u.level_id 使用了下划线）
                if (isset($data['levelId'])) {
                    $data['level_id'] = $data['levelId'];
                    unset($data['levelId']);
                }
                return $data;
            })
            ->toArray();
    }

    /**
     * 分类数据
     */
    public function getCategoriesData(): array
    {
        return Category::orderBy('name')
            ->get()
            ->map(fn ($c) => $this->snakeToCamel($c->toArray()))
            ->toArray();
    }

    /**
     * 评论数据（最新 100 条）
     */
    public function getCommentsData(): array
    {
        return Comment::orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(fn ($c) => $this->snakeToCamel($c->toArray()))
            ->toArray();
    }

    /**
     * 标签数据
     */
    public function getTagsData(): array
    {
        return Tag::orderBy('name')
            ->get()
            ->map(fn ($t) => $this->snakeToCamel($t->toArray()))
            ->toArray();
    }

    /**
     * 标签关联数据（多态中间表 taggables）
     */
    public function getTaggablesData(): array
    {
        $rows = DB::table('taggables')
            ->select('tag_id', 'taggable_id', 'taggable_type')
            ->get()
            ->toArray();

        return array_map(fn ($r) => $this->snakeToCamel((array) $r), $rows);
    }

    /**
     * 访问记录数据（最新 500 条）
     *
     * DB 字段：page, title, ip_address, user_agent, referrer, created_at
     * 前端期望：ip, visitedAt, page, title, duration, source
     */
    public function getVisitsData(): array
    {
        return Visit::orderBy('created_at', 'desc')
            ->limit(500)
            ->get()
            ->map(function ($visit) {
                return [
                    'ip'        => $visit->ip_address ?? '0.0.0.0',
                    'visitedAt' => $visit->created_at?->toISOString() ?? now()->toISOString(),
                    'page'      => $visit->page
                        ?? $visit->visitable_type
                        ? str_replace('App\\Models\\', '', $visit->visitable_type)
                        : 'home',
                    'title'     => $visit->title
                        ?? $visit->visitable_type
                        ? str_replace('App\\Models\\', '', $visit->visitable_type)
                        : 'Home',
                    'duration'  => 0,
                    'source'    => $visit->referrer ? parse_url($visit->referrer, PHP_URL_HOST) ?? 'direct' : 'direct',
                ];
            })
            ->toArray();
    }

    /**
     * 每日流量聚合数据（90 天完整填充范围，按配置时区分组）
     *
     * 不再依赖前端用浏览器时区生成日期 key，后端统一以配置时区生成完整的 90 天日期序列，
     * 无数据的天填 0，前端直接切片使用即可，彻底消除时区不一致导致的"显示未到的日期"问题。
     *
     * 返回格式：[{ day: '3/6', visits: 42, unique: 15 }, { day: '3/7', visits: 0, unique: 0 }, ...]
     */
    public function getDailyTrafficData(): array
    {
        $tz = $this->settingService->get('timezone', 'UTC');
        $now = Carbon::now($tz);

        // 90 天前在配置时区中的 UTC 边界
        $startDate = $now->copy()->subDays(90)->startOfDay()->setTimezone('UTC');

        // 查询原始数据（created_at 存储为 UTC 时间戳）
        $rows = Visit::where('created_at', '>=', $startDate)
            ->get(['created_at', 'ip_address']);

        // 使用配置时区进行按日分组
        $dailyMap = [];
        foreach ($rows as $row) {
            $day = $row->created_at->setTimezone($tz)->format('n/j');
            if (!isset($dailyMap[$day])) {
                $dailyMap[$day] = ['visits' => 0, 'ips' => []];
            }
            $dailyMap[$day]['visits']++;
            $dailyMap[$day]['ips'][$row->ip_address] = true;
        }

        // 生成完整 90 天日期序列（配置时区），无数据的天填 0
        $result = [];
        $cursor = $now->copy()->subDays(90)->startOfDay();
        for ($i = 0; $i <= 90; $i++) {
            $day = $cursor->format('n/j');
            $result[] = [
                'day'    => $day,
                'visits' => $dailyMap[$day]['visits'] ?? 0,
                'unique' => isset($dailyMap[$day]) ? count($dailyMap[$day]['ips']) : 0,
            ];
            $cursor->addDay();
        }

        return $result;
    }

    /**
     * 用户等级数据
     */
    public function getUserLevelsData(): array
    {
        return UserLevel::orderBy('level')
            ->get()
            ->map(function ($level) {
                $data = $this->snakeToCamel($level->toArray());
                // 前端的 getUserLevelDistribution 中访问 level.name, level.level, level.icon 等
                return $data;
            })
            ->toArray();
    }

    /**
     * 角色数据（Spatie RBAC）
     *
     * 前端期望字段：id, label, value, description
     * Spatie 角色字段：id, name, guard_name
     */
    public function getRolesData(): array
    {
        return Role::all()
            ->map(fn ($role) => [
                'id'          => $role->id,
                'label'       => ucfirst($role->name),
                'value'       => $role->name,
                'description' => "{$role->name} role (guard: {$role->guard_name})",
            ])
            ->toArray();
    }

    /**
     * 活动日志数据（最新 20 条，spatie/activitylog）
     */
    public function getLogsData(): array
    {
        return Activity::with('causer')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($log) {
                $data = $this->snakeToCamel($log->toArray());
                // 添加 causer 用户名
                if ($log->causer) {
                    $data['causerName'] = $log->causer->name;
                }
                return $data;
            })
            ->toArray();
    }

    // ─── 环比变化率计算 ──────────────────────────────────────────

    /**
     * 计算各项指标的环比变化率
     *
     * 对比近 30 天 vs 前 30 天的数量变化，返回格式化字符串如 "+12%" / "-5%" / "0%"
     */
    public function getPeriodChanges(): array
    {
        $now = now();
        $currentStart = (clone $now)->subDays(30);
        $previousStart = (clone $now)->subDays(60);

        return [
            // 内容统计
            'totalPosts'       => $this->formatChange(Post::where('created_at', '>=', $currentStart)->count(), Post::whereBetween('created_at', [$previousStart, $currentStart])->count()),
            'totalVideos'      => $this->formatChange(Video::where('created_at', '>=', $currentStart)->count(), Video::whereBetween('created_at', [$previousStart, $currentStart])->count()),
            'totalProjects'    => $this->formatChange(Project::where('created_at', '>=', $currentStart)->count(), Project::whereBetween('created_at', [$previousStart, $currentStart])->count()),
            'totalResources'   => $this->formatChange(Resource::where('created_at', '>=', $currentStart)->count(), Resource::whereBetween('created_at', [$previousStart, $currentStart])->count()),

            // 用户统计
            'totalUsers'       => $this->formatChange(User::where('created_at', '>=', $currentStart)->count(), User::whereBetween('created_at', [$previousStart, $currentStart])->count()),
            'activeUsers'      => $this->formatChange(User::where('status', 'active')->where('created_at', '>=', $currentStart)->count(), User::where('status', 'active')->whereBetween('created_at', [$previousStart, $currentStart])->count()),
            'newUsers'         => $this->formatChange(User::where('created_at', '>=', $currentStart)->count(), User::whereBetween('created_at', [$previousStart, $currentStart])->count()),
            'subscriberCount'  => 0, // 暂无订阅功能

            // 流量统计
            'totalVisits'      => $this->formatChange(Visit::where('created_at', '>=', $currentStart)->count(), Visit::whereBetween('created_at', [$previousStart, $currentStart])->count()),
            'uniqueVisitors'   => $this->formatChange(Visit::where('created_at', '>=', $currentStart)->distinct('ip_address')->count('ip_address'), Visit::whereBetween('created_at', [$previousStart, $currentStart])->distinct('ip_address')->count('ip_address')),
            'pageViews'        => $this->formatChange($this->sumViewsCount($currentStart, $now), $this->sumViewsCount($previousStart, $currentStart)),
            'avgDuration'      => '0%', // duration 暂未追踪
        ];
    }

    /**
     * 格式化变化率为 "+12%" / "-5%" / "0%" 格式
     */
    private function formatChange(int $current, int $previous): string
    {
        if ($previous === 0) {
            return $current > 0 ? '+100%' : '0%';
        }
        $change = round((($current - $previous) / $previous) * 100);
        return ($change >= 0 ? '+' : '') . $change . '%';
    }

    /**
     * 汇总所有内容模型的 views_count（指定时间范围）
     */
    private function sumViewsCount($start, $end): int
    {
        return (int) Post::whereBetween('created_at', [$start, $end])->sum('views_count')
            + (int) Video::whereBetween('created_at', [$start, $end])->sum('views_count')
            + (int) Project::whereBetween('created_at', [$start, $end])->sum('views_count');
    }

    // ─── 工具方法 ────────────────────────────────────────────────

    /**
     * 递归将数组的 snake_case 键转为 camelCase
     */
    private function snakeToCamel(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $camelKey = $this->toCamelCase($key);

            if (is_array($value) && $this->isAssoc($value)) {
                $result[$camelKey] = $this->snakeToCamel($value);
            } else {
                $result[$camelKey] = $value;
            }
        }

        return $result;
    }

    /**
     * snake_case → camelCase
     */
    private function toCamelCase(string $string): string
    {
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }

    /**
     * 判断是否为关联数组
     */
    private function isAssoc(array $arr): bool
    {
        if (empty($arr)) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
