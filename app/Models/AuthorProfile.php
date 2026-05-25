<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorProfile extends Model
{
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
}
