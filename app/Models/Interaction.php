<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Interaction extends Model
{
    protected $fillable = [
        'user_id',
        'visitor_id',
        'ip_address',
        'user_agent',
        'interactable_id',
        'interactable_type',
        'type',
    ];

    /**
     * Get the user that owns the interaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent interactable model (Post, Project, etc.).
     */
    public function interactable(): MorphTo
    {
        return $this->morphTo();
    }
}
