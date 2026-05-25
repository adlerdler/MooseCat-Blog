<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdInteraction extends Model
{
    protected $fillable = [
        'advertisement_id',
        'user_id',
        'type',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the advertisement that owns the interaction.
     */
    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    /**
     * Get the user that owns the interaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for clicks.
     */
    public function scopeClicks($query)
    {
        return $query->where('type', 'click');
    }

    /**
     * Scope for views.
     */
    public function scopeViews($query)
    {
        return $query->where('type', 'view');
    }
}
