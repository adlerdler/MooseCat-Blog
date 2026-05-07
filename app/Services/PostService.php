<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    /**
     * 获取文章列表（带分页和筛选）
     */
    public function getPaginatedPosts(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Post::query()
            ->with(['author', 'category', 'tags'])
            ->when($filters['category'] ?? null, fn($q, $slug) => $q->whereHas('category', fn($q) => $q->where('slug', $slug)))
            ->when($filters['tag'] ?? null, fn($q, $slug) => $q->whereHas('tags', fn($q) => $q->where('slug', $slug)))
            ->when($filters['status'] ?? 'published', fn($q, $status) => $q->where('status', $status))
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * 创建新文章
     */
    public function createPost(array $data): Post
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
            
            $post = Post::create($data);

            if (isset($data['tags'])) {
                $post->tags()->sync($data['tags']);
            }

            return $post;
        });
    }

    /**
     * 更新文章
     */
    public function updatePost(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data) {
            if (isset($data['title']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            $post->update($data);

            if (isset($data['tags'])) {
                $post->tags()->sync($data['tags']);
            }

            return $post;
        });
    }

    /**
     * 发布文章
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
     */
    public function incrementViews(Post $post): void
    {
        $post->increment('views_count');
    }
}
