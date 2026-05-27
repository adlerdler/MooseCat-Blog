<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * UserRepository - 用户数据访问层
 * 
 * 封装用户相关的复杂查询逻辑，包括分页查询、角色筛选等功能。
 * Encapsulates complex query logic related to users, including paginated queries, 
 * role filtering and other functionalities.
 */
class UserRepository
{
    /**
     * 创建新的数据访问层实例
     * Create a new repository instance
     */
    public function __construct()
    {
        //
    }

    /**
     * 获取分页用户列表（带筛选）
     * Get paginated users with filters
     */
    public function getPaginatedUsers(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return User::query()
            ->with(['roles', 'profile'])
            ->when($filters['role'] ?? null, fn($q, $role) => $q->whereHas('roles', fn($q) => $q->where('name', $role)))
            ->when($filters['email'] ?? null, fn($q, $email) => $q->where('email', 'like', "%{$email}%"))
            ->when($filters['name'] ?? null, fn($q, $name) => $q->where('name', 'like', "%{$name}%"))
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * 获取活跃用户
     * Get active users
     */
    public function getActiveUsers(int $limit = 10): Collection
    {
        return User::query()
            ->with(['roles'])
            ->where('is_active', true)
            ->latest('last_active_at')
            ->limit($limit)
            ->get();
    }

    /**
     * 根据邮箱获取用户
     * Get user by email
     */
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * 搜索用户
     * Search users
     */
    public function searchUsers(string $keyword, int $limit = 10): Collection
    {
        return User::query()
            ->with(['roles'])
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('email', 'like', '%' . $keyword . '%');
            })
            ->limit($limit)
            ->get();
    }

    /**
     * 获取有特定角色的用户
     * Get users with specific role
     */
    public function getUsersByRole(string $roleName): Collection
    {
        return User::query()
            ->with(['roles', 'profile'])
            ->whereHas('roles', fn($q) => $q->where('name', $roleName))
            ->get();
    }
}