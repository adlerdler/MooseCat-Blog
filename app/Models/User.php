<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'level_id',
        'status',
        'points',
        'notifications',
        'comment_approval_alert',
        'new_user_alert',
        'weekly_report',
        'digest_email',
        'digest_frequency',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 禁止自动加载 notifications 关系（与 notifications 布尔列冲突）。
     * 关系仅通过 $user->notifications() 显式查询获取。
     *
     * @var array<int, string>
     */
    protected $with = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'notifications' => 'boolean',
            'comment_approval_alert' => 'boolean',
            'new_user_alert' => 'boolean',
            'weekly_report' => 'boolean',
            'digest_email' => 'boolean',
        ];
    }

    /**
     * 注册媒体集合（管理后台媒体库上传到此）
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->acceptsMimeTypes([
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'video/mp4', 'video/webm',
                'audio/mpeg', 'audio/wav',
                'application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
            ]);
    }

    /**
     * 注册媒体转换（仅为图片生成缩略图）
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->performOnCollections('default');

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->withResponsiveImages()
            ->performOnCollections('default');
    }

    /**
     * Get the posts for the user.
     */
    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    /**
     * Get the comments for the user.
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the interactions for the user.
     */
    public function interactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    /**
     * Get the social accounts for the user (OAuth).
     */
    public function socialAccounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get the journal entries for the user.
     */
    public function journals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Journal::class);
    }

    /**
     * Get the author profile for the user.
     */
    public function authorProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AuthorProfile::class);
    }

    /**
     * 覆盖 toArray() 确保 notifications 列不与 Notifiable trait 的关系冲突。
     */
    public function toArray(): array
    {
        // 先调用父类 toArray，它内部 merge 了 attributes + relations
        $data = parent::toArray();

        // 如果 relations 中存在 notifications（多态通知关系），
        // 用 attributes 中的布尔值覆盖，避免关系集合覆盖布尔首选项
        if (isset($data['notifications']) && !is_bool($data['notifications'])) {
            $data['notifications'] = $this->getAttribute('notifications');
        }

        return $data;
    }

    /**
     * Get the user level for the user.
     */
    public function userLevel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(UserLevel::class, 'level_id');
    }

    /**
     * Get the points history for the user.
     */
    public function pointsHistory(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserPointsHistory::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('users')
            ->logExcept(['password', 'password_confirmation', 'remember_token']);
    }
}
