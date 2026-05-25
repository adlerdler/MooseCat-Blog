<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'mood',
        'weather',
        'date',
        'is_public',
        'likes_count',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'date' => 'date',
    ];

    /**
     * Get the user that owns the journal entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
