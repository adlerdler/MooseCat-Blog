<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $fillable = [
        'filename',
        'size',
        'status',
        'type',
        'started_at',
        'completed_at',
        'error_message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Scope for successful backups.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }
}
