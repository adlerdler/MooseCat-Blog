<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Auth\Events\Login;
use Spatie\Activitylog\Models\Activity;
use App\Events\CommentCreated;
use App\Events\SeoFilesNeedRegenerate;
use App\Listeners\SendCommentNotification;
use App\Listeners\AwardCommentPoints;
use App\Listeners\UpdateLastLoginAt;
use App\Listeners\RegenerateSeoFiles;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Gate::before(function ($user, $ability) {
            return $user instanceof User && $user->isAdministrator() ? true : null;
        });

        // 模型 LogsActivity trait 事件触发时不会自动填写 IP/UA，
        // 通过 saving 钩子统一补填（中间件通过 activity() helper 已自带）
        Activity::saving(function (Activity $activity) {
            if (request() && ! $activity->ip_address) {
                $activity->ip_address = request()->ip();
                $activity->user_agent = request()->userAgent();
            }
        });

        // 注册事件监听：评论创建时发送通知给文章作者
        Event::listen(
            CommentCreated::class,
            SendCommentNotification::class,
        );

        // 注册事件监听：评论创建时给评论者加积分
        Event::listen(
            CommentCreated::class,
            AwardCommentPoints::class,
        );

        // 注册事件监听：登录时更新 last_login_at + 每日登录积分
        Event::listen(
            Login::class,
            UpdateLastLoginAt::class,
        );

        // 注册事件监听：SEO 文件重新生成
        Event::listen(
            SeoFilesNeedRegenerate::class,
            RegenerateSeoFiles::class,
        );
    }
}
