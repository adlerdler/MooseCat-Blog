<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\UserLevel;
use App\Models\UserPointsHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function getPaginatedUsers(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return User::query()
            ->with(['roles', 'profile'])
            ->when($filters['role'] ?? null, fn($q, $role) => $q->whereHas('roles', fn($q) => $q->where('name', $role)))
            ->when($filters['email'] ?? null, fn($q, $email) => $q->where('email', 'like', "%{$email}%"))
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function getUserById(int $id): ?User
    {
        return User::with(['roles', 'profile'])->find($id);
    }

    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function getUsers(array $filters = []): Collection
    {
        return User::query()
            ->when($filters['role'] ?? null, fn($q, $role) => $q->whereHas('roles', fn($q) => $q->where('name', $role)))
            ->latest('created_at')
            ->get();
    }

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

    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            
            $roleId = $data['role_id'] ?? null;
            unset($data['role_id']);
            
            $user->update($data);
            
            if ($roleId) {
                $user->syncRoles([$roleId]);
            } elseif (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }
            
            return $user;
        });
    }

    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            $user->roles()->detach();
            return $user->delete();
        });
    }

    public function updateAvatar(User $user, string $avatarPath): User
    {
        return DB::transaction(function () use ($user, $avatarPath) {
            $user->update(['avatar' => $avatarPath]);
            return $user;
        });
    }

    public function changePassword(User $user, string $newPassword): bool
    {
        return $user->update(['password' => Hash::make($newPassword)]);
    }

    public function verifyPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }

    public function addPoints(User $user, int $points, string $type = 'earning', string $description = ''): void
    {
        DB::transaction(function () use ($user, $points, $type, $description) {
            $originalPoints = $user->points ?? 0;
            $user->increment('points', $points);
            
            UserPointsHistory::create([
                'user_id' => $user->id,
                'points' => $points,
                'type' => $type,
                'description' => $description ?: '积分增加',
            ]);

            $this->updateUserLevel($user);
        });
    }

    public function deductPoints(User $user, int $points, string $description = ''): void
    {
        DB::transaction(function () use ($user, $points, $description) {
            $user->decrement('points', $points);
            
            UserPointsHistory::create([
                'user_id' => $user->id,
                'points' => -$points,
                'type' => 'spending',
                'description' => $description ?: '积分减少',
            ]);

            $this->updateUserLevel($user);
        });
    }

    public function updateUserLevel(User $user): void
    {
        $level = UserLevel::getLevelForPoints($user->points ?? 0);
        
        if ($level && $user->level_id !== $level->id) {
            $user->update(['level_id' => $level->id]);
        }
    }

    public function getPointsHistory(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return $user->pointsHistory()
            ->latest('created_at')
            ->paginate($perPage);
    }
}