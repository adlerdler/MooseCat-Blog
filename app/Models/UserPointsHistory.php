<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPointsHistory extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'type',
        'description',
    ];

    /**
     * Get the user that owns the points history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for earning points.
     */
    public function scopeEarning($query)
    {
        return $query->where('points', '>', 0);
    }

    /**
     * Scope for spending points.
     */
    public function scopeSpending($query)
    {
        return $query->where('points', '<', 0);
    }
}
