<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InteractionService
{
    /**
     * 切换点赞状态
     */
    public function toggleLike(User $user, Model $interactable): bool
    {
        return DB::transaction(function () use ($user, $interactable) {
            $interaction = $user->interactions()
                ->where('interactable_id', $interactable->id)
                ->where('interactable_type', get_class($interactable))
                ->where('type', 'like')
                ->first();

            if ($interaction) {
                $interaction->delete();
                $interactable->decrement('likes_count');
                return false; // 已取消点赞
            }

            $user->interactions()->create([
                'interactable_id' => $interactable->id,
                'interactable_type' => get_class($interactable),
                'type' => 'like',
            ]);

            $interactable->increment('likes_count');
            return true; // 已点赞
        });
    }

    /**
     * 切换收藏状态
     */
    public function toggleBookmark(User $user, Model $interactable): bool
    {
        return DB::transaction(function () use ($user, $interactable) {
            $interaction = $user->interactions()
                ->where('interactable_id', $interactable->id)
                ->where('interactable_type', get_class($interactable))
                ->where('type', 'bookmark')
                ->first();

            if ($interaction) {
                $interaction->delete();
                return false; // 已取消收藏
            }

            $user->interactions()->create([
                'interactable_id' => $interactable->id,
                'interactable_type' => get_class($interactable),
                'type' => 'bookmark',
            ]);

            return true; // 已收藏
        });
    }
}
