<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Services\PointsService;

/**
 * AwardCommentPoints - 评论积分奖励监听器
 *
 * 评论时给评论者 +5 积分。
 */
class AwardCommentPoints
{
    public function __construct(
        protected PointsService $pointsService,
    ) {}

    public function handle(CommentCreated $event): void
    {
        $comment = $event->comment;

        if (! $comment->user_id) {
            return; // 访客评论不计分
        }

        $this->pointsService->awardPoints(
            user: $comment->user,
            points: 5,
            type: 'comment',
            description: '发表评论',
            referenceId: $comment->id,
            referenceType: $comment::class,
        );
    }
}
