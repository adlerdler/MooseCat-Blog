<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use App\Models\Subscriber;
use App\Events\SeoFilesNeedRegenerate;
use App\Jobs\SendEmailJob;
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
            $slug = $data['slug'] ?? (Str::random(8) . '-' . Str::random(4));
            while (Post::where('slug', $slug)->exists()) {
                $slug = Str::random(8) . '-' . Str::random(4);
            }
            $data['slug'] = $slug;
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

            // 新文章直接发布，通知所有订阅者
            if ($post->status === 'published') {
                $this->notifySubscribers($post, 'new');
                SeoFilesNeedRegenerate::dispatch();
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
            $wasPublished = $post->status === 'published';
            $nowPublished = ($data['status'] ?? $post->status) === 'published';

            // 当状态设为 published 且原文章无发布时间时，自动记录
            if ($nowPublished && !$post->published_at) {
                $data['published_at'] = $data['published_at'] ?? now();
            }

            $tags = $data['tags'] ?? [];
            unset($data['tags']);

            $post->update($data);

            if (!empty($tags)) {
                $post->tags()->sync($this->tagService->resolveTagIds($tags));
            }

            // 文章状态变更为发布时，通知所有订阅者
            if (!$wasPublished && $nowPublished) {
                $this->notifySubscribers($post, 'new');
            }

            // 已发布文章更新时，重新生成 SEO 文件
            if ($wasPublished || $nowPublished) {
                SeoFilesNeedRegenerate::dispatch();
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
        $result = $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        // 发布时通知所有订阅者
        $this->notifySubscribers($post, 'new');
        SeoFilesNeedRegenerate::dispatch();

        return $result;
    }

    /**
     * 增加文章浏览量
     * Increment post view count
     */
    public function incrementViews(Post $post): void
    {
        $post->increment('views_count');
    }

    /**
     * 通知所有活跃订阅者文章已发布
     * Notify all active subscribers about a published post.
     */
    private function notifySubscribers(Post $post, string $type = 'new'): void
    {
        $subscribers = Subscriber::where('is_active', true)->pluck('email');

        if ($subscribers->isEmpty()) {
            return;
        }

        // 确保 category 关系已加载
        if (!$post->relationLoaded('category')) {
            $post->load('category');
        }

        $postUrl = url('/posts/' . $post->slug);
        $brandName = config('app.name', 'Arkhyx');
        $timestamp = now()->format('Y-m-d H:i');

        $subject = $type === 'new'
            ? "新文章 | {$post->title}"
            : "文章更新 | {$post->title}";

        $htmlBody = view('emails.new-post', [
            'title'      => $post->title,
            'excerpt'    => $post->excerpt ?? mb_substr(strip_tags($post->content ?? ''), 0, 200),
            'postUrl'    => $postUrl,
            'actionText' => '阅读全文',
            'brandName'  => $brandName,
            'category'   => $post->category?->name ?? 'GENERAL',
            'timestamp'  => $timestamp,
        ])->render();

        // 逐个推入异步队列发送给订阅者（保留收件人隐私，且由 Queue Worker 异步限速发送）
        foreach ($subscribers as $email) {
            SendEmailJob::dispatch($email, $subject, $htmlBody);
        }
    }
}