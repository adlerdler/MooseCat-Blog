<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

/**
 * CommentRepository - 评论数据访问层
 * 
 * 封装评论相关的复杂查询逻辑，包括评论树、待审核评论等功能。
 * Encapsulates complex query logic related to comments, including comment tree, 
 * pending approval comments and other functionalities.
 */
class CommentRepository
{
    /**
     * 创建新的数据访问层实例
     * Create a new repository instance
     */
    public function __construct()
    {
        //
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
     * 获取待审核评论
     * Get pending approval comments
     */
    public function getPendingComments(int $limit = null): Collection
    {
        $query = Comment::query()
            ->with(['user', 'commentable'])
            ->where('is_approved', false)
            ->latest();

        if ($limit !== null) {
            return $query->limit($limit)->get();
        }

        return $query->get();
    }

    /**
     * 获取用户的评论
     * Get user's comments
     */
    public function getUserComments(int $userId, int $limit = null): Collection
    {
        $query = Comment::query()
            ->where('user_id', $userId)
            ->with(['commentable'])
            ->latest();

        if ($limit !== null) {
            return $query->limit($limit)->get();
        }

        return $query->get();
    }

    /**
     * 统计待审核评论数
     * Count pending approval comments
     */
    public function countPendingComments(): int
    {
        return Comment::where('is_approved', false)->count();
    }

    /**
     * 批量获取评论（带分页）
     * Get paginated comments
     */
    public function getPaginatedComments(int $perPage = 15, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Comment::query()
            ->with(['user', 'commentable'])
            ->when($filters['approved'] ?? null, function ($q) use ($filters) {
                $q->where('is_approved', $filters['approved']);
            })
            ->when($filters['keyword'] ?? null, function ($q) use ($filters) {
                $q->where('body', 'like', '%' . $filters['keyword'] . '%');
            })
            ->latest()
            ->paginate($perPage);
    }
}