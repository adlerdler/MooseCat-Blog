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
    /**
     * 获取仪表盘全部数据
     */
    public function getDashboardData(): array
    {
        return [
            'posts'      => $this->getPostsData(),
            'videos'     => $this->getVideosData(),
            'projects'   => $this->getProjectsData(),
            'resources'  => $this->getResourcesData(),
            'users'      => $this->getUsersData(),
            'categories' => $this->getCategoriesData(),
            'comments'   => $this->getCommentsData(),
            'tags'       => $this->getTagsData(),
            'taggables'  => $this->getTaggablesData(),
            'visits'     => $this->getVisitsData(),
            'userLevels' => $this->getUserLevelsData(),
            'roles'      => $this->getRolesData(),
            'logs'       => $this->getLogsData(),
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
