<?php

namespace App\Policies;

use App\Models\Medium;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MediaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-media');
    }

    public function view(User $user, Medium $medium): bool
    {
        return $user->can('view-media');
    }

    public function create(User $user): bool
    {
        return $user->can('upload-media');
    }

    public function update(User $user, Medium $medium): bool
    {
        return $user->can('edit-media');
    }

    public function delete(User $user, Medium $medium): bool
    {
        return $user->can('delete-media');
    }

    public function restore(User $user, Medium $medium): bool
    {
        return $user->can('delete-media');
    }

    public function forceDelete(User $user, Medium $medium): bool
    {
        return $user->can('delete-media');
    }
}
