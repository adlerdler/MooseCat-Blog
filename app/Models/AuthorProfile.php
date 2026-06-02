<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AuthorProfile extends Model
{
    use LogsActivity;
    protected $fillable = [
        'user_id',
        'slug',
        'display_name',
        'bio',
        'avatar',
        'role_label',
        'role_title',
        'company',
        'status_label',
        'status_text',
        'is_active',
        'social_links',
        'expertise',
        'skills',
        'manifestos',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'social_links' => 'array',
        'expertise' => 'array',
        'skills' => 'array',
        'manifestos' => 'array',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 头像访问器：无头像时根据 slug 哈希生成 identicon 头像
     * 同一 slug 永远生成相同图案
     */
    public function getAvatarAttribute($value): string
    {
        if (!empty($value)) {
            return $value;
        }

        $seed = md5($this->slug ?? 'default');
        return "https://api.dicebear.com/9.x/identicon/svg?seed={$seed}";
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('author-profiles');
    }
}
