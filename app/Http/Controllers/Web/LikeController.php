<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Interaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    /**
     * 切换点赞状态
     * 同 IP + 同浏览器 = 不可重复点赞（visitor_id 唯一索引保证）
     * 同 IP + 不同浏览器 = 可以点赞（visitor_id 不同）
     */
    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'interactable_type' => 'required|string',
            'interactable_id'   => 'required|integer',
        ]);

        $type = $validated['interactable_type'];
        $id   = $validated['interactable_id'];
        $ip   = $request->ip();
        $ua   = $request->userAgent() ?? '';
        $visitorId = md5($ip . $ua . $type . $id . 'like');

        $liked = DB::transaction(function () use ($visitorId, $type, $id, $ip, $ua) {
            $existing = Interaction::where('visitor_id', $visitorId)->first();

            if ($existing) {
                // 取消点赞
                $existing->delete();
                $this->decrementLikesCount($type, $id);
                return false;
            }

            // 新增点赞
            Interaction::create([
                'visitor_id'        => $visitorId,
                'interactable_type' => $type,
                'interactable_id'   => $id,
                'type'              => 'like',
                'ip_address'        => $ip,
                'user_agent'        => $ua,
            ]);

            $this->incrementLikesCount($type, $id);
            return true;
        });

        return response()->json([
            'liked'       => $liked,
            'likes_count' => $this->getLikesCount($type, $id),
        ]);
    }

    private function getLikesCount(string $type, int $id): int
    {
        $model = $type::find($id);
        return $model->likes_count ?? 0;
    }

    private function incrementLikesCount(string $type, int $id): void
    {
        $model = $type::find($id);
        if ($model && method_exists($model, 'increment')) {
            $model->increment('likes_count');
        }
    }

    private function decrementLikesCount(string $type, int $id): void
    {
        $model = $type::find($id);
        if ($model && method_exists($model, 'decrement')) {
            $model->decrement('likes_count');
        }
    }
}
