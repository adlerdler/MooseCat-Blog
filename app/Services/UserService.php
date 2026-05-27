<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * UserService - 用户服务类
 * 
 * 提供用户的管理功能，包括用户列表、创建、更新、删除、密码管理和角色分配。
 * Provides user management functionality, including user listing, creation, update, 
 * deletion, password management and role assignment.
 */
class UserService
{
    /**
     * 获取用户列表（带分页和筛选）
     * Get paginated user list with filters
     */
    public function getPaginatedUsers(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return User::query()
            ->with(['roles', 'profile'])
            ->when($filters['role'] ?? null, fn($q, $role) => $q->whereHas('roles', fn($q) => $q->where('name', $role)))
            ->when($filters['email'] ?? null, fn($q, $email) => $q->where('email', 'like', "%{$email}%"))
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * 根据ID获取用户
     * Get user by ID
     */
    public function getUserById(int $id): ?User
    {
        return User::with(['roles', 'profile'])->find($id);
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
     * 获取所有用户
     * Get all users
     */
    public function getUsers(array $filters = []): Collection
    {
        return User::query()
            ->when($filters['role'] ?? null, fn($q, $role) => $q->whereHas('roles', fn($q) => $q->where('name', $role)))
            ->latest('created_at')
            ->get();
    }

    /**
     * 创建用户
     * Create user
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $data['email_verified_at'] = $data['email_verified_at'] ?? now();
            $user = User::create($data);
            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }
            return $user;
        });
    }

    /**
     * 更新用户
     * Update user
     */
    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            $user->update($data);
            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }
            return $user;
        });
    }

    /**
     * 删除用户
     * Delete user
     */
    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            $user->roles()->detach();
            return $user->delete();
        });
    }

    /**
     * 更新用户头像
     * Update user avatar
     */
    public function updateAvatar(User $user, string $avatarPath): User
    {
        return DB::transaction(function () use ($user, $avatarPath) {
            $user->update(['avatar' => $avatarPath]);
            return $user;
        });
    }

    /**
     * 修改密码
     * Change password
     */
    public function changePassword(User $user, string $newPassword): bool
    {
        return $user->update(['password' => Hash::make($newPassword)]);
    }

    /**
     * 验证密码
     * Verify password
     */
    public function verifyPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }
}