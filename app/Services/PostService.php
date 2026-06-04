<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * PostService - 文章服务类
 * 
 * 提供文章内容的管理功能，包括文章列表、创建、更新、发布和浏览量统计。
 * Provides post content management functionality, including post listing, creation, 
 * update, publication and view count statistics.
 */
class PostService
{
    public function __construct(protected TagService $tagService)
    {
    }

    /**
     * 获取文章列表（带分页和筛选）
     * Get paginated post list with filters
     */
    public function getPaginatedPosts(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Post::query()
            ->with(['author', 'category', 'tags'])
            ->when($filters['author_id'] ?? null, fn($q, $id) => $q->where('author_id', $id))
            ->when($filters['category'] ?? null, fn($q, $slug) => $q->whereHas('category', fn($q) => $q->where('slug', $slug)))
            ->when($filters['tag'] ?? null, fn($q, $slug) => $q->whereHas('tags', fn($q) => $q->where('slug', $slug)))
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * 创建新文章
     * Create new post
     */
    public function createPost(array $data): Post
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $data['slug'] ?? (Str::random(8) . '-' . Str::random(4));
            $data['status'] = $data['status'] ?? 'published';
            $data['published_at'] = $data['published_at'] ?? now();

            $tags = $data['tags'] ?? [];
            unset($data['tags']);

            // SEO 默认值：未提供时自动从标题/摘要/标签填充
            $data['meta_title']       = ($data['meta_title'] ?? '')       ?: ($data['title'] ?? '');
            $data['meta_description'] = ($data['meta_description'] ?? '') ?: ($data['excerpt'] ?? '');
            $data['meta_keywords']    = ($data['meta_keywords'] ?? '')    ?: (is_array($tags) ? implode(', ', $tags) : '');

            $post = Post::create($data);

            if (!empty($tags)) {
                $post->tags()->sync($this->tagService->resolveTagIds($tags));
            }

            return $post;
        });
    }

    /**
     * 更新文章
     * Update post
     */
    public function updatePost(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data) {
            // 编辑时不自动生成 slug，保持原有值
            // 当状态设为 published 且原文章无发布时间时，自动记录
            if (($data['status'] ?? $post->status) === 'published' && !$post->published_at) {
                $data['published_at'] = $data['published_at'] ?? now();
            }

            $tags = $data['tags'] ?? [];
            unset($data['tags']);

            $post->update($data);

            if (!empty($tags)) {
                $post->tags()->sync($this->tagService->resolveTagIds($tags));
            }

            return $post;
        });
    }

    /**
     * 发布文章
     * Publish post
     */
    public function publishPost(Post $post): bool
    {
        return $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * 增加文章浏览量
     * Increment post view count
     */
    public function incrementViews(Post $post): void
    {
        $post->increment('views_count');
    }
}