<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class CommentService
{
    /**
     * 为指定文章提交评论
     */
    public function createComment(Post $post, array $data): Comment
    {
        return DB::transaction(function () use ($post, $data) {
            $comment = $post->comments()->create([
                'user_id' => $data['user_id'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'body' => $data['body'],
                'ip_address' => $data['ip_address'] ?? null,
                'user_agent' => $data['user_agent'] ?? null,
                'is_approved' => config('blog.auto_approve_comments', true),
            ]);

            return $comment;
        });
    }

    /**
     * 获取文章的审核通过的评论树
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
     */
    public function approveComment(Comment $comment): bool
    {
        return $comment->update(['is_approved' => true]);
    }

    /**
     * 批量审核评论
     */
    public function bulkApprove(array $ids): int
    {
        return Comment::whereIn('id', $ids)->update(['is_approved' => true]);
    }

    /**
     * 删除评论（及其子评论）
     */
    public function deleteComment(Comment $comment): bool
    {
        return DB::transaction(function () use ($comment) {
            // 如果有子评论，Laravel 的级联删除（如果在迁移中定义了）会自动处理，
            // 但为了安全起见，我们也可以手动处理或依赖数据库约束。
            return $comment->delete();
        });
    }
}
