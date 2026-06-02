<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Visit extends Model
{
    const UPDATED_AT = null;
    
    protected $fillable = [
        'visitable_id',
        'visitable_type',
        'page',
        'title',
        'ip_address',
        'user_agent',
        'referrer',
    ];

    /**
     * Get the owning visitable model.
     */
    public function visitable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for a specific IP address.
     */
    public function scopeIpAddress($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Scope for today's visits.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
