<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
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
            $user->update($data);
            if (isset($data['roles'])) {
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
}
