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
        'bio',
        'avatar',
        'role_label',
        'role_title',
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('author-profiles');
    }
}
