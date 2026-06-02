<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

/**
 * CommentService - 评论服务类
 * 
 * 提供评论的管理功能，包括评论创建、审核、删除和评论树展示。
 * Provides comment management functionality, including comment creation, approval, 
 * deletion and comment tree display.
 *
 * 审核逻辑：
 * - comment_approval = false（禁止审核）→ 评论自动通过，直接显示
 * - comment_approval = true（开启审核）→ 评论需管理员后台审核
 */
class CommentService
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    /**
     * 为指定文章提交评论
     * Submit comment for specified post
     */
    public function createComment(Post $post, array $data): Comment
    {
        return DB::transaction(function () use ($post, $data) {
            // 从数据库设置读取审核开关：
            // comment_approval = false（禁止审核）→ is_approved = true（直接显示）
            // comment_approval = true（开启审核）→ is_approved = false（需要审核）
            $requiresApproval = (bool) $this->settingService->get('comment_approval', false);
            $isApproved = !$requiresApproval;

            $comment = $post->comments()->create([
                'user_id' => $data['user_id'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'body' => $data['body'],
                'ip_address' => $data['ip_address'] ?? null,
                'user_agent' => $data['user_agent'] ?? null,
                'is_approved' => $isApproved,
            ]);

            return $comment;
        });
    }

    /**
     * 获取文章的审核通过的评论树
     * Get approved comment tree for post
     */
    public function getApprovedCommentsForPost(Post $post): Collection
    {
        return $post->comments()
            ->where('is_approved', true)
            ->whereNull('parent_id')
            ->with(['children' => fn($q) => $q->where('is_approved', true), 'user'])
            ->oldest()
            ->get();
    }

    /**
     * 审核评论
     * Approve comment
     */
    public function approveComment(Comment $comment): bool
    {
        return $comment->update(['is_approved' => true]);
    }

    /**
     * 批量审核评论
     * Bulk approve comments
     */
    public function bulkApprove(array $ids): int
    {
        return Comment::whereIn('id', $ids)->update(['is_approved' => true]);
    }

    /**
     * 管理员回复评论
     * Create admin reply to a comment
     */
    public function createAdminReply(Comment $parent, array $data): Comment
    {
        return DB::transaction(function () use ($parent, $data) {
            $reply = $parent->children()->create([
                'post_id' => $parent->post_id,
                'user_id' => $data['user_id'] ?? null,
                'name' => $data['name'] ?? 'Admin',
                'email' => $data['email'] ?? null,
                'body' => $data['body'],
                'ip_address' => $data['ip_address'] ?? null,
                'user_agent' => $data['user_agent'] ?? null,
                'is_approved' => true,
                'is_admin' => true,
            ]);

            return $reply;
        });
    }

    /**
     * 删除评论（及其子评论）
     * Delete comment (and its children)
     */
    public function deleteComment(Comment $comment): bool
    {
        return DB::transaction(function () use ($comment) {
            // 如果有子评论，Laravel 的级联删除（如果在迁移中定义了）会自动处理，
            // 但为了安全起见，我们也可以手动处理或依赖数据库约束。
            // If there are child comments, Laravel's cascade delete (if defined in migration) will handle automatically,
            // but for safety, we can also handle it manually or rely on database constraints.
            return $comment->delete();
        });
    }
}