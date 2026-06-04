<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Interaction;
use App\Models\User;
use App\Services\PointsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    public function __construct(
        protected PointsService $pointsService,
    ) {}
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
        $userId = Auth::check() ? Auth::id() : null;
        $visitorId = md5($ip . $ua . $type . $id . 'like');

        $liked = DB::transaction(function () use ($userId, $visitorId, $type, $id, $ip, $ua) {
            // 登录用户按 user_id 查重，访客按 visitor_id 查重
            $existing = $userId
                ? Interaction::where('user_id', $userId)
                    ->where('interactable_type', $type)
                    ->where('interactable_id', $id)
                    ->where('type', 'like')
                    ->first()
                : Interaction::where('visitor_id', $visitorId)->first();

            if ($existing) {
                // 取消点赞
                $existing->delete();
                $this->decrementLikesCount($type, $id);
                return false;
            }

            // 新增点赞
            Interaction::create([
                'user_id'           => $userId,
                'visitor_id'        => $visitorId,
                'interactable_type' => $type,
                'interactable_id'   => $id,
                'type'              => 'like',
                'ip_address'        => $ip,
                'user_agent'        => $ua,
            ]);

            $this->incrementLikesCount($type, $id);

            // ─── 积分奖励：点赞者 +2 ────
            if ($userId) {
                $liker = User::find($userId);
                if ($liker) {
                    $this->pointsService->awardPoints(
                        user: $liker,
                        points: 2,
                        type: 'like',
                        description: '点赞内容',
                        referenceId: $id,
                        referenceType: $type,
                    );
                }
            }

            // ─── 积分奖励：被点赞者 +3（仅登录用户发表的内容）────
            $model = $type::find($id);
            if ($model) {
                $authorId = $model->author_id
                    ?? $model->user_id
                    ?? null;

                if ($authorId && (int) $authorId !== (int) $userId) {
                    $author = User::find($authorId);
                    if ($author) {
                        $this->pointsService->awardPoints(
                            user: $author,
                            points: 3,
                            type: 'liked',
                            description: '内容被点赞',
                            referenceId: $id,
                            referenceType: $type,
                        );
                    }
                }
            }

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
