<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'github',
        'twitter',
        'linkedin',
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
}
