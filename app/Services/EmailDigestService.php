<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\SendEmailJob;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Project;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * EmailDigestService - 周报和摘要邮件服务
 *
 * 负责生成统计数据、新内容列表，并按用户偏好发送个性化邮件。
 */
class EmailDigestService
{
    protected string $brandName;
    protected string $siteUrl;

    public function __construct()
    {
        $this->brandName = $this->getBrandName();
        $this->siteUrl = rtrim(config('app.url', ''), '/');
    }

    /**
     * 生成并发送周报（给管理员）
     */
    public function sendWeeklyReport(): int
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $stats = $this->getWeeklyStats($weekStart, $weekEnd);
        $newPosts = $this->getNewPosts($weekStart, $weekEnd);
        $topComments = $this->getTopComments($weekStart, $weekEnd);
        $newSubscribers = $this->getNewSubscribers($weekStart, $weekEnd);

        $dateRange = $weekStart->format('m/d') . ' - ' . $weekEnd->format('m/d, Y');
        $periodLabel = '本周';

        $htmlBody = view('emails.weekly-report', [
            'brandName'    => $this->brandName,
            'periodLabel'  => $periodLabel,
            'dateRange'    => $dateRange,
            'stats'        => $stats,
            'newPosts'     => $newPosts,
            'topComments'  => $topComments,
            'newSubscribers' => $newSubscribers,
            'dashboardUrl' => $this->siteUrl . '/admin/dashboard',
            'timestamp'    => now()->format('Y-m-d H:i'),
        ])->render();

        $subject = "📊 {$periodLabel}数据报告 | {$dateRange}";

        // 发送给所有开启周报的用户
        $users = User::where('weekly_report', true)->get();

        foreach ($users as $user) {
            if (!$user->email || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            // 检查总邮件开关
            if ($user->notifications === false) {
                continue;
            }

            dispatch(new SendEmailJob($user->email, $subject, $htmlBody))->afterResponse();

            Log::info('Weekly report email dispatched', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);
        }

        return $users->count();
    }

    /**
     * 生成并发送摘要邮件（给订阅用户）
     */
    public function sendDigestEmail(string $frequency = 'weekly'): int
    {
        $frequencyMap = [
            'daily'   => ['start' => Carbon::now()->startOfDay(), 'end' => Carbon::now()->endOfDay(), 'label' => '今日'],
            'weekly'  => ['start' => Carbon::now()->startOfWeek(), 'end' => Carbon::now()->endOfWeek(), 'label' => '本周'],
            'monthly' => ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()->endOfMonth(), 'label' => '本月'],
        ];

        $period = $frequencyMap[$frequency] ?? $frequencyMap['weekly'];
        $periodStart = $period['start'];
        $periodEnd = $period['end'];

        $dateRange = $periodStart->format('m/d') . ' - ' . $periodEnd->format('m/d, Y');

        // 获取开启摘要邮件的用户
        $users = User::where('digest_email', true)
            ->where('digest_frequency', $frequency)
            ->whereNotNull('email')
            ->get();

        $sentCount = 0;

        foreach ($users as $user) {
            // 检查总邮件开关
            if ($user->notifications === false) {
                continue;
            }

            $personalStats = $this->getPersonalStats($user, $periodStart, $periodEnd);
            $newPosts = $this->getNewPosts($periodStart, $periodEnd, 3);
            $newVideos = $this->getNewVideos($periodStart, $periodEnd, 3);
            $newProjects = $this->getNewProjects($periodStart, $periodEnd, 3);

            $greeting = $this->getGreeting();

            $htmlBody = view('emails.digest', [
                'brandName'    => $this->brandName,
                'greeting'     => $greeting,
                'userName'     => $user->name,
                'periodLabel'  => $period['label'],
                'dateRange'    => $dateRange,
                'newPosts'     => $newPosts,
                'newVideos'    => $newVideos,
                'newProjects'  => $newProjects,
                'personalStats' => $personalStats,
                'siteUrl'      => $this->siteUrl,
                'unsubscribeUrl' => $this->siteUrl . '/unsubscribe?email=' . urlencode($user->email),
                'timestamp'    => now()->format('Y-m-d H:i'),
            ])->render();

            $subject = "{$greeting} {$user->name}，{$period['label']}内容摘要";

            dispatch(new SendEmailJob($user->email, $subject, $htmlBody))->afterResponse();

            $sentCount++;

            Log::info('Digest email dispatched', [
                'user_id'   => $user->id,
                'email'     => $user->email,
                'frequency' => $frequency,
            ]);
        }

        return $sentCount;
    }

    /**
     * 获取本周统计数据
     */
    protected function getWeeklyStats(Carbon $start, Carbon $end): array
    {
        return [
            [
                'label' => '文章',
                'value' => Post::whereBetween('published_at', [$start, $end])->count(),
            ],
            [
                'label' => '评论',
                'value' => Comment::whereBetween('created_at', [$start, $end])->count(),
            ],
            [
                'label' => '订阅',
                'value' => Subscriber::whereBetween('subscribed_at', [$start, $end])->count(),
            ],
            [
                'label' => '新用户',
                'value' => User::whereBetween('created_at', [$start, $end])->count(),
            ],
        ];
    }

    /**
     * 获取本周新发布的文章
     */
    protected function getNewPosts(Carbon $start, Carbon $end, int $limit = 10): array
    {
        return Post::with('author')
            ->where('status', 'published')
            ->whereBetween('published_at', [$start, $end])
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($post) => [
                'title'       => $post->title,
                'excerpt'     => $post->excerpt,
                'url'         => $this->siteUrl . '/blog/' . ($post->slug ?? $post->id),
                'author'      => $post->author?->name ?? '未知作者',
                'published_at' => $post->published_at?->format('m/d H:i'),
            ])
            ->toArray();
    }

    /**
     * 获取本周热门评论
     */
    protected function getTopComments(Carbon $start, Carbon $end, int $limit = 5): array
    {
        return Comment::with('post')
            ->where('is_approved', true)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($comment) => [
                'body'       => mb_strlen($comment->body) > 100
                    ? mb_substr($comment->body, 0, 100) . '...'
                    : $comment->body,
                'author'     => $comment->name,
                'post_title' => $comment->post?->title ?? '未知文章',
            ])
            ->toArray();
    }

    /**
     * 获取本周新订阅者
     */
    protected function getNewSubscribers(Carbon $start, Carbon $end, int $limit = 10): array
    {
        return Subscriber::whereBetween('subscribed_at', [$start, $end])
            ->orderBy('subscribed_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($sub) => [
                'email' => $sub->email,
                'name'  => $sub->name,
            ])
            ->toArray();
    }

    /**
     * 获取新视频
     */
    protected function getNewVideos(Carbon $start, Carbon $end, int $limit = 3): array
    {
        return Video::where('status', 'published')
            ->whereBetween('published_at', [$start, $end])
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($video) => [
                'title'       => $video->title,
                'description' => mb_strlen($video->description ?? '') > 80
                    ? mb_substr($video->description, 0, 80) . '...'
                    : ($video->description ?? ''),
                'url'         => $this->siteUrl . '/videos/' . ($video->slug ?? $video->id),
            ])
            ->toArray();
    }

    /**
     * 获取新项目
     */
    protected function getNewProjects(Carbon $start, Carbon $end, int $limit = 3): array
    {
        return Project::where('status', 'published')
            ->whereBetween('published_at', [$start, $end])
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($project) => [
                'title'       => $project->title,
                'description' => mb_strlen($project->description ?? '') > 80
                    ? mb_substr($project->description, 0, 80) . '...'
                    : ($project->description ?? ''),
                'url'         => $this->siteUrl . '/projects/' . ($project->slug ?? $project->id),
            ])
            ->toArray();
    }

    /**
     * 获取用户个人统计
     */
    protected function getPersonalStats(User $user, Carbon $start, Carbon $end): ?array
    {
        return [
            'comments' => Comment::where('user_id', $user->id)
                ->whereBetween('created_at', [$start, $end])
                ->count(),
            'likes'   => $user->interactions()
                ->where('type', 'like')
                ->whereBetween('created_at', [$start, $end])
                ->count(),
            'points'  => number_format($user->points),
        ];
    }

    /**
     * 根据时间段生成问候语
     */
    protected function getGreeting(): string
    {
        $hour = now()->hour;

        return match (true) {
            $hour < 6  => '夜深了',
            $hour < 9  => '早上好',
            $hour < 12 => '上午好',
            $hour < 14 => '中午好',
            $hour < 18 => '下午好',
            $hour < 21 => '晚上好',
            default    => '你好',
        };
    }

    /**
     * 获取站点品牌名称
     */
    protected function getBrandName(): string
    {
        $settingName = \App\Models\Setting::value('name');
        return $settingName ?: config('app.name', 'ARKHYX');
    }
}
